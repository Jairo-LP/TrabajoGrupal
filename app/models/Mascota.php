<?php
class Mascota {
    private $conn;
    private $table = 'mascotas';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function listarPorDueno($id_duenio) {
        $query = "SELECT * FROM " . $this->table . " WHERE id_duenio = :id_duenio ORDER BY nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_duenio', $id_duenio);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // TODO [Sprint 2 - RF02]: Implementar método crear($nombre, $especie, $raza, $edad, $id_duenio)

    public function crear($nombre, $especie, $raza, $edad, $id_duenio) {
        $query = "INSERT INTO " . $this->table . " (nombre, especie, raza, edad, id_duenio) VALUES (:nombre, :especie, :raza, :edad, :id_duenio)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':especie', $especie);
        $stmt->bindParam(':raza', $raza);
        $stmt->bindParam(':edad', $edad);
        $stmt->bindParam(':id_duenio', $id_duenio);
        return $stmt->execute();
    }

    
}
