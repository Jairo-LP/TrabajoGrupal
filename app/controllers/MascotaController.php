<?php
require_once __DIR__ . '/../models/Mascota.php';

class MascotaController {
    private $mascotaModel;

    public function __construct($db) {
        $this->mascotaModel = new Mascota($db);
    }

    public function certificar() {
        if (!isset($_SESSION['usuario'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $usuario = $_SESSION['usuario'];
        $rolesPermitidos = ['veterinario', 'recepcionista'];

        if (!in_array($usuario['rol'], $rolesPermitidos, true)) {
            $_SESSION['flash_error'] = 'No tienes permisos para certificar mascotas.';
            header('Location: index.php?action=dashboard');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idMascota = filter_input(INPUT_POST, 'id_mascota', FILTER_VALIDATE_INT);
            $observacion = trim($_POST['observacion_certificado'] ?? '');

            if (!$idMascota || $observacion === '') {
                $_SESSION['flash_error'] = 'Debes seleccionar una mascota válida e ingresar una observación.';
                header('Location: index.php?action=dashboard');
                exit;
            }

            $mascota = $this->mascotaModel->obtenerPorId($idMascota);
            if (!$mascota) {
                $_SESSION['flash_error'] = 'La mascota indicada no existe.';
                header('Location: index.php?action=dashboard');
                exit;
            }

            $ok = $this->mascotaModel->certificar($idMascota, $observacion, (int)$usuario['id']);

            if ($ok) {
                $_SESSION['flash_success'] = 'Mascota "' . $mascota['nombre'] . '" certificada correctamente.';
            } else {
                $_SESSION['flash_error'] = 'No se pudo emitir el certificado. Intenta nuevamente.';
            }
        }

        header('Location: index.php?action=dashboard');
        exit;
    }

    public function verCertificado() {
        if (!isset($_SESSION['usuario'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $idMascota = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$idMascota) {
            $_SESSION['flash_error'] = 'Identificador de mascota no válido.';
            header('Location: index.php?action=dashboard');
            exit;
        }

        $mascota = $this->mascotaModel->obtenerPorId($idMascota);
        $usuario = $_SESSION['usuario'];

        if (!$mascota || (int)$mascota['certificada'] !== 1) {
            $_SESSION['flash_error'] = 'Esta mascota aún no cuenta con un certificado emitido.';
            header('Location: index.php?action=dashboard');
            exit;
        }

        if ($usuario['rol'] === 'cliente' && (int)$mascota['id_dueno'] !== (int)$usuario['id']) {
            $_SESSION['flash_error'] = 'No tienes permiso para ver este certificado.';
            header('Location: index.php?action=dashboard');
            exit;
        }

        require __DIR__ . '/../views/mascotas/certificado.php';
    }
}