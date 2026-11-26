<?php
require_once __DIR__ . '/../models/Cita.php';
require_once __DIR__ . '/../models/Mascota.php';
require_once __DIR__ . '/../models/Usuario.php';

class CitaController {
    private $citaModel;
    private $mascotaModel;
    private $usuarioModel;

    public function __construct($db) {
        $this->citaModel = new Cita($db);
        $this->mascotaModel = new Mascota($db);
        $this->usuarioModel = new Usuario($db);
    }

    public function dashboard() {
        if (!isset($_SESSION['usuario'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $usuario = $_SESSION['usuario'];
        $veterinarios = $this->usuarioModel->listarVeterinarios();

        if ($usuario['rol'] === 'cliente') {
            $citas = $this->citaModel->listarPorDueno($usuario['id']);
            $mascotas = $this->mascotaModel->listarPorDueno($usuario['id']);
        } else {
            $citas = $this->citaModel->listarTodas();
            $mascotas = $this->mascotaModel->listarTodas();
        }

        $mensajeCita = $_SESSION['mensaje_cita'] ?? null;
        unset($_SESSION['mensaje_cita']);

        require __DIR__ . '/../views/dashboard/index.php';
    }

    // TODO [Sprint 2 - RF03]: Método agendarCita()
    public function cambiarEstadoCita() {
        if (!isset($_SESSION['usuario'])) {
            header('Location: index.php?action=login');
            exit;
        }

        if ($_SESSION['usuario']['rol'] !== 'recepcionista') {
            http_response_code(403);
            exit('No tienes permiso para actualizar el estado de la cita.');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            header('Allow: POST');
            exit('Método no permitido.');
        }

        $idCita = filter_var($_POST['id_cita'] ?? null, FILTER_VALIDATE_INT);
        $estado = $_POST['estado'] ?? '';

        if ($idCita === false || $idCita === null ||
            !in_array($estado, ['confirmada', 'cancelada'], true)) {
            $_SESSION['mensaje_cita'] = [
                'tipo' => 'danger',
                'texto' => 'La solicitud para actualizar la cita no es válida.'
            ];
        } elseif ($this->citaModel->recepcionistaActualizarEstado($idCita, $estado)) {
            $_SESSION['mensaje_cita'] = [
                'tipo' => 'success',
                'texto' => $estado === 'confirmada'
                    ? 'La cita fue aceptada.'
                    : 'La cita fue rechazada.'
            ];
        } else {
            $_SESSION['mensaje_cita'] = [
                'tipo' => 'danger',
                'texto' => 'No se pudo actualizar la cita. Puede que ya no esté pendiente.'
            ];
        }

        header('Location: index.php?action=dashboard');
        exit;
    }
}
