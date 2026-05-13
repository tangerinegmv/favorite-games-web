<?php

require_once 'SecurityHelper.php';

SecurityHelper::initSecureSession();
SecurityHelper::requireLogin();

// Validate CSRF token
if (empty($_POST['csrf_token']) || !SecurityHelper::validateCSRFToken($_POST['csrf_token'])) {
    SecurityHelper::logSecurityEvent('CSRF token validation failed on preference save', 'warning');
    header("refresh:3;url=preferencias.php");
    echo '<p>Error de seguridad: Token CSRF inválido. Por favor, intente de nuevo.</p>';
    exit;
}

// Validate genero field exists and is not empty
if (empty($_POST['genero'])) {
    header("refresh:3;url=preferencias.php");
    echo '<p>Error: Debe seleccionar un género.</p>';
    exit;
}

$usuario = $_SESSION['usuario'] ?? null;
$genero = $_POST['genero'];

if (!$usuario) {
    header("refresh:3;url=../index.php");
    echo '<p>Error: Sesión no válida.</p>';
    exit;
}

// Set cookie with preference
$cookie_name = htmlspecialchars($usuario, ENT_QUOTES, 'UTF-8');
$cookie_value = htmlspecialchars($genero, ENT_QUOTES, 'UTF-8');

setcookie($cookie_name, $cookie_value, [
    'expires' => time() + (86400 * 30),  // 30 days
    'path' => '/',
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'httponly' => true,  // Prevent JavaScript access
    'samesite' => 'Strict'
]);

SecurityHelper::logSecurityEvent('User preference saved for genre: ' . $genero, 'info');

header("refresh:3;url=juego_listado.php");
echo '<p>Preferencia guardada exitosamente.</p>';

?>