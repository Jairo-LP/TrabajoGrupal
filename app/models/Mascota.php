<?php
class Mascota {
    private $conn;
    private $table = 'mascotas';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function listarPorDueno($id_dueno) {
        $query = "SELECT m.*, u.nombre AS nombre_certificador
                  FROM " . $this->table . " m
                  LEFT JOIN usuarios u ON m.certificado_por = u.id
                  WHERE m.id_dueno = :id_dueno
                  ORDER BY m.nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_dueno', $id_dueno, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarTodas() {
        $query = "SELECT m.*, d.nombre AS nombre_dueno, u.nombre AS nombre_certificador
                  FROM " . $this->table . " m
                  INNER JOIN usuarios d ON m.id_dueno = d.id
                  LEFT JOIN usuarios u ON m.certificado_por = u.id
                  ORDER BY m.certificada ASC, m.nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $query = "SELECT m.*, d.nombre AS nombre_dueno, d.email AS email_dueno, u.nombre AS nombre_certificador
                  FROM " . $this->table . " m
                  INNER JOIN usuarios d ON m.id_dueno = d.id
                  LEFT JOIN usuarios u ON m.certificado_por = u.id
                  WHERE m.id = :id
                  LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($nombre, $especie, $raza, $edad, $id_dueno) {
        $query = "INSERT INTO " . $this->table . " (nombre, especie, raza, edad, id_dueno)
                  VALUES (:nombre, :especie, :raza, :edad, :id_dueno)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':especie', $especie);
        $stmt->bindParam(':raza', $raza);
        $stmt->bindParam(':edad', $edad, PDO::PARAM_INT);
        $stmt->bindParam(':id_dueno', $id_dueno, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function certificar($id_mascota, $observacion, $id_certificador) {
        $codigo = 'CERT-' . date('Ymd') . '-' . str_pad((string)$id_mascota, 4, '0', STR_PAD_LEFT) . '-' . strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));

        $query = "UPDATE " . $this->table . "
                  SET certificada = 1,
                      codigo_certificado = :codigo,
                      observacion_certificado = :observacion,
                      fecha_certificacion = NOW(),
                      certificado_por = :certificado_por
                  WHERE id = :id_mascota";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':observacion', $observacion);
        $stmt->bindParam(':certificado_por', $id_certificador, PDO::PARAM_INT);
        $stmt->bindParam(':id_mascota', $id_mascota, PDO::PARAM_INT);
        return $stmt->execute();
    }
}