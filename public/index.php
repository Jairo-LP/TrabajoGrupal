<?php
session_start();

require_once __DIR__ . '/../app/config/Database.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/CitaController.php';
require_once __DIR__ . '/../app/controllers/MascotaController.php';

$database = new Database();
$db = $database->getConnection();

$action = $_GET['action'] ?? 'dashboard';

$authController = new AuthController($db);
$citaController = new CitaController($db);
$mascotaController = new MascotaController($db);

switch ($action) {
    case 'login':
        $authController->login();
        break;
    case 'logout':
        $authController->logout();
        break;
    case 'certificar_mascota':
        $mascotaController->certificar();
        break;
    case 'ver_certificado':
        $mascotaController->verCertificado();
        break;
    case 'cambiar_estado_cita':
        $citaController->cambiarEstadoCita();
        break;
    case 'dashboard':
    default:
        $citaController->dashboard();
        break;
}