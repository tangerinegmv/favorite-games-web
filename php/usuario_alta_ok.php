<?php

require_once 'SecurityHelper.php';
require_once 'conexion.php';

SecurityHelper::initSecureSession();
SecurityHelper::requireAdmin();

// Validate CSRF token
if (empty($_POST['csrf_token']) || !SecurityHelper::validateCSRFToken($_POST['csrf_token'])) {
    SecurityHelper::logSecurityEvent('CSRF token validation failed on user creation', 'warning');
    header("refresh:3;url=usuario_alta.php");
    echo '<p>Error de seguridad: Token CSRF inválido. Por favor, intente de nuevo.</p>';
    exit;
}

// Validate required fields
if (empty($_POST['usuario']) || empty($_POST['pass']) || empty($_POST['tipo'])) {
    header("refresh:3;url=usuario_alta.php");
    echo '<p>Error: Campos requeridos faltantes.</p>';
    exit;
}

// Validate username format
if (!SecurityHelper::validateUsername($_POST['usuario'])) {
    header("refresh:3;url=usuario_alta.php");
    echo '<p>Error: Nombre de usuario inválido. Solo se permiten letras, números, guiones y guiones bajos.</p>';
    exit;
}

// Validate user type
if (!SecurityHelper::validateUserType($_POST['tipo'])) {
    header("refresh:3;url=usuario_alta.php");
    echo '<p>Error: Tipo de usuario inválido.</p>';
    exit;
}

// Validate password strength
$password = $_POST['pass'];
if (strlen($password) < 6) {
    header("refresh:3;url=usuario_alta.php");
    echo '<p>Error: La contraseña debe tener al menos 6 caracteres.</p>';
    exit;
}

// Hash password with bcrypt
$clave = SecurityHelper::hashPassword($password);
if (!$clave) {
    header("refresh:3;url=usuario_alta.php");
    echo '<p>Error: No se pudo procesar la contraseña.</p>';
    exit;
}

$usuario = $_POST['usuario'];
$tipo = $_POST['tipo'];
$foto_nombre = '';

$cnn = conectar();
if (!$cnn) {
    header("refresh:3;url=usuario_alta.php");
    echo '<p>Error: No se pudo conectar a la base de datos.</p>';
    exit;
}

// Check if username already exists
$check_sql = 'SELECT id_usuario FROM usuario WHERE usuario = ?';
$check_stmt = mysqli_prepare($cnn, $check_sql);
mysqli_stmt_bind_param($check_stmt, 's', $usuario);
mysqli_stmt_execute($check_stmt);
mysqli_stmt_store_result($check_stmt);

if (mysqli_stmt_num_rows($check_stmt) > 0) {
    desconectar($cnn);
    header("refresh:3;url=usuario_alta.php");
    echo '<p>Error: El nombre de usuario ya existe.</p>';
    exit;
}

// Handle file upload if present
if (!empty($_FILES['foto']['size'])) {
    $validation = SecurityHelper::validateFileUpload($_FILES['foto']);
    if (!$validation['valid']) {
        desconectar($cnn);
        header("refresh:3;url=usuario_alta.php");
        echo '<p>Error: ' . htmlspecialchars($validation['error'], ENT_QUOTES, 'UTF-8') . '</p>';
        exit;
    }

    $foto_nombre = SecurityHelper::sanitizeFilename($_FILES['foto']['name'], $usuario);
    if (!$foto_nombre) {
        desconectar($cnn);
        header("refresh:3;url=usuario_alta.php");
        echo '<p>Error: Nombre de archivo inválido.</p>';
        exit;
    }

    $rutaO = $_FILES['foto']['tmp_name'];
    $destino = '../img/usuarios/' . $foto_nombre;
    if (!move_uploaded_file($rutaO, $destino)) {
        desconectar($cnn);
        header("refresh:3;url=usuario_alta.php");
        echo '<p>Error: No se pudo guardar la foto.</p>';
        SecurityHelper::logSecurityEvent('Failed to upload file for new user: ' . $usuario, 'error');
        exit;
    }
}

$sql = 'INSERT INTO usuario (usuario, pass, tipo, foto, activado) VALUES (?, ?, ?, ?, "S")';
$sentencia = mysqli_prepare($cnn, $sql);
mysqli_stmt_bind_param($sentencia, 'ssss', $usuario, $clave, $tipo, $foto_nombre);
$resultado = mysqli_stmt_execute($sentencia);

if ($resultado) {
    SecurityHelper::logSecurityEvent('New user created: ' . $usuario, 'info');
    desconectar($cnn);
    header("refresh:3;url=usuario_listado.php");
    echo '<p>Usuario creado exitosamente.</p>';
} else {
    SecurityHelper::logSecurityEvent('User creation failed for: ' . $usuario, 'error');
    desconectar($cnn);
    header("refresh:3;url=usuario_alta.php");
    echo '<p>Error al crear el usuario.</p>';
}

?>