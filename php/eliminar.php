<?php
require_once 'SecurityHelper.php';
require_once 'conexion.php';

SecurityHelper::initSecureSession();
SecurityHelper::requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('refresh:3;url=usuario_listado.php');
    echo '<p>Error: Método de solicitud inválido.</p>';
    exit;
}

if (empty($_POST['csrf_token']) || !SecurityHelper::validateCSRFToken($_POST['csrf_token'])) {
    SecurityHelper::logSecurityEvent('CSRF token validation failed on user delete', 'warning');
    header('refresh:3;url=usuario_listado.php');
    echo '<p>Error de seguridad: Token CSRF inválido.</p>';
    exit;
}

$id = SecurityHelper::validateInteger($_POST['id_usuario'] ?? null);
if (!$id) {
    header('refresh:3;url=usuario_listado.php');
    echo '<p>Error: ID de usuario inválido.</p>';
    exit;
}

$cnn = conectar();
if (!$cnn) {
    header('refresh:3;url=usuario_listado.php');
    echo '<p>Error: No se pudo conectar a la base de datos.</p>';
    exit;
}

$sql = 'UPDATE usuario SET activado = "N" WHERE id_usuario = ?';
$sentencia = mysqli_prepare($cnn, $sql);
mysqli_stmt_bind_param($sentencia, 'i', $id);
$resultado = mysqli_stmt_execute($sentencia);

if ($resultado) {
    SecurityHelper::logSecurityEvent('User deactivated via delete action: ' . $id, 'info');
    header('refresh:3;url=usuario_listado.php');
    echo '<p>Usuario eliminado exitosamente.</p>';
} else {
    SecurityHelper::logSecurityEvent('Failed user delete action for ID: ' . $id, 'error');
    header('refresh:3;url=usuario_listado.php');
    echo '<p>No se pudo eliminar el usuario.</p>';
}

desconectar($cnn);
?>


