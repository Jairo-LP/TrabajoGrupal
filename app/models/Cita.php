<?php
class Cita {
    private $conn;
    private $table = 'citas';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function listarTodas() {
        $query = "SELECT c.*, m.nombre AS mascota, m.especie, u.nombre AS dueno, v.nombre AS veterinario
                  FROM " . $this->table . " c
                  INNER JOIN mascotas m ON c.id_mascota = m.id
                  INNER JOIN usuarios u ON m.id_dueno = u.id
                  INNER JOIN usuarios v ON c.id_veterinario = v.id
                  ORDER BY c.fecha_hora DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function listarPorDueno($id_duenio) {
        $query = "SELECT c.*, m.nombre AS mascota, m.especie, v.nombre AS veterinario
                  FROM " . $this->table . " c
                  INNER JOIN mascotas m ON c.id_mascota = m.id
                  INNER JOIN usuarios v ON c.id_veterinario = v.id
                  WHERE m.id_duenio = :id_duenio
                  ORDER BY c.fecha_hora DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_duenio', $id_duenio);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // TODO [Sprint 2 - RF04]: Implementar verificarDisponibilidad($id_veterinario, $fecha_hora)
    
    // TODO [Sprint 2 - RF03]: Implementar crear($id_mascota, $id_veterinario, $fecha_hora, $motivo)
    public function crear($id_mascota, $id_veterinario, $fecha_hora, $motivo) {
        $query = "INSERT INTO " . $this->table . " (id_mascota, id_veterinario, fecha_hora, motivo) VALUES (:id_mascota, :id_veterinario, :fecha_hora, :motivo)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_mascota', $id_mascota);
        $stmt->bindParam(':id_veterinario', $id_veterinario);
        $stmt->bindParam(':fecha_hora', $fecha_hora);
        $stmt->bindParam(':motivo', $motivo);
        return $stmt->execute();
    }
    public function recepcionistaActualizarEstado($id_cita, $estado) {
        if (!in_array($estado, ['confirmada', 'cancelada'], true)) {
            return false;
        }

        $query = "UPDATE " . $this->table . "
                  SET estado = :estado
                  WHERE id = :id_cita AND estado = 'pendiente'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':id_cita', $id_cita);
        return $stmt->execute();
    } 
