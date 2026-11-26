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

        require __DIR__ . '/../views/dashboard/index.php';
    }

    // TODO [Sprint 2 - RF03]: Método agendarCita()

    public function cambiarEstadoCita() {
        if (!isset($_SESSION['usuario'])) {
            header('Location: index.php?action=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=dashboard');
            exit;
        }

        $usuario = $_SESSION['usuario'];
        $idCita = filter_input(INPUT_POST, 'id_cita', FILTER_VALIDATE_INT);
        $estado = trim($_POST['estado'] ?? '');

        if (!$idCita) {
            $_SESSION['flash_error'] = 'Identificador de cita no válido.';
            header('Location: index.php?action=dashboard');
            exit;
        }

        $cita = $this->citaModel->obtenerPorId($idCita);
        if (!$cita) {
            $_SESSION['flash_error'] = 'La cita indicada no existe.';
            header('Location: index.php?action=dashboard');
            exit;
        }

        $transiciones = [
            'pendiente' => ['confirmada', 'cancelada', 'atendida'],
            'confirmada' => ['cancelada', 'atendida'],
            'cancelada' => [],
            'atendida' => []
        ];
        $estadoActual = $cita['estado'];

        if (!isset($transiciones[$estadoActual]) || !in_array($estado, $transiciones[$estadoActual], true)) {
            $_SESSION['flash_error'] = 'No se puede cambiar una cita ' . $estadoActual . ' a ' . $estado . '.';
            header('Location: index.php?action=dashboard');
            exit;
        }

        $notaClinica = null;
        if ($usuario['rol'] === 'recepcionista' && in_array($estado, ['confirmada', 'cancelada'], true)) {
            $notaClinica = null;
        } elseif ($usuario['rol'] === 'veterinario' && $estado === 'atendida' && (int)$cita['id_veterinario'] === (int)$usuario['id']) {
            $notaClinica = trim($_POST['nota_clinica'] ?? '');
            if ($notaClinica === '') {
                $_SESSION['flash_error'] = 'Debes ingresar la nota clínica para marcar la cita como atendida.';
                header('Location: index.php?action=dashboard');
                exit;
            }
        } else {
            $_SESSION['flash_error'] = 'No tienes permisos para realizar este cambio de estado.';
            header('Location: index.php?action=dashboard');
            exit;
        }

        $ok = $this->citaModel->actualizarEstado($idCita, $estado, $notaClinica);
        if ($ok) {
            $_SESSION['flash_success'] = 'Cita #' . $idCita . ' marcada como ' . $estado . ' correctamente.';
        } else {
            $_SESSION['flash_error'] = 'No se pudo actualizar el estado de la cita.';
        }

        header('Location: index.php?action=dashboard');
        exit;
    }
}
