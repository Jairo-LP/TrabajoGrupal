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

    public function listarPorDueno($id_dueno) {
        $query = "SELECT c.*, m.nombre AS mascota, m.especie, v.nombre AS veterinario 
                  FROM " . $this->table . " c 
                  INNER JOIN mascotas m ON c.id_mascota = m.id 
                  INNER JOIN usuarios v ON c.id_veterinario = v.id 
                  WHERE m.id_dueno = :id_dueno 
                  ORDER BY c.fecha_hora DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_dueno', $id_dueno);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Validar si el veterinario está disponible en esa fecha y hora
    public function verificarDisponibilidad($id_veterinario, $fecha_hora) {
        $query = "SELECT COUNT(*) AS total 
                  FROM " . $this->table . " 
                  WHERE id_veterinario = :id_veterinario 
                  AND fecha_hora = :fecha_hora
                  AND estado != 'cancelada'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_veterinario', $id_veterinario);
        $stmt->bindParam(':fecha_hora', $fecha_hora);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Retorna true si el total es 0 (está disponible), false si ya hay una cita
        return $row['total'] == 0;
    }

    // [Sprint 2 - RF03]: Crear una nueva cita validando disponibilidad
    public function crear($id_mascota, $id_veterinario, $fecha_hora, $motivo) {
        // 1. Verificar disponibilidad antes de insertar
        if (!$this->verificarDisponibilidad($id_veterinario, $fecha_hora)) {
            return false; // O puedes lanzar una excepción: throw new Exception("Horario no disponible");
        }

        // 2. Insertar la cita si está disponible
        $query = "INSERT INTO " . $this->table . " (id_mascota, id_veterinario, fecha_hora, motivo) 
                  VALUES (:id_mascota, :id_veterinario, :fecha_hora, :motivo)";
        
        $stmt = $this->conn->prepare($query);

        $motivo = htmlspecialchars(strip_tags($motivo));

        $stmt->bindParam(':id_mascota', $id_mascota);
        $stmt->bindParam(':id_veterinario', $id_veterinario);
        $stmt->bindParam(':fecha_hora', $fecha_hora);
        $stmt->bindParam(':motivo', $motivo);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function obtenerPorId($id) {
        $query = "SELECT c.*
                  FROM " . $this->table . " c
                  WHERE c.id = :id
                  LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function actualizarEstado($id_cita, $estado, $nota_clinica = null) {
        $estadosPermitidos = ['pendiente', 'confirmada', 'cancelada', 'atendida'];
        if (!in_array($estado, $estadosPermitidos, true)) {
            return false;
        }

        if ($nota_clinica === null) {
            $query = "UPDATE " . $this->table . "
                      SET estado = :estado
                      WHERE id = :id";
        } else {
            $query = "UPDATE " . $this->table . "
                      SET estado = :estado, nota_clinica = :nota_clinica
                      WHERE id = :id";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':estado', $estado);
        if ($nota_clinica !== null) {
            $nota_clinica = htmlspecialchars(strip_tags($nota_clinica), ENT_QUOTES, 'UTF-8');
            $stmt->bindParam(':nota_clinica', $nota_clinica);
        }
        $stmt->bindParam(':id', $id_cita, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

    
