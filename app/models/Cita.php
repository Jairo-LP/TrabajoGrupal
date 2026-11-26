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

    // [Sprint 2 - RF03]: Crear una nueva cita
    public function crear($id_mascota, $id_veterinario, $fecha_hora, $motivo) {
        $query = "INSERT INTO " . $this->table . " (id_mascota, id_veterinario, fecha_hora, motivo) 
                  VALUES (:id_mascota, :id_veterinario, :fecha_hora, :motivo)";
        
        $stmt = $this->conn->prepare($query);

        // Limpieza básica de datos
        $motivo = htmlspecialchars(strip_tags($motivo));

        // Vinculación de parámetros
        $stmt->bindParam(':id_mascota', $id_mascota);
        $stmt->bindParam(':id_veterinario', $id_veterinario);
        $stmt->bindParam(':fecha_hora', $fecha_hora);
        $stmt->bindParam(':motivo', $motivo);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}

    
