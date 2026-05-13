<?php

require_once 'SecurityHelper.php';
require_once 'conexion.php';

SecurityHelper::initSecureSession();

// Validate CSRF token
if (empty($_POST['csrf_token']) || !SecurityHelper::validateCSRFToken($_POST['csrf_token'])) {
    SecurityHelper::logSecurityEvent('CSRF token validation failed on user update', 'warning');
    header("refresh:3;url=usuario_listado.php");
    echo '<p>Error de seguridad: Token CSRF inválido. Por favor, intente de nuevo.</p>';
    exit;
}

// Validate required fields
if (empty($_POST['usuario']) || empty($_POST['tipo']) || empty($_POST['id'])) {
    header("refresh:3;url=usuario_listado.php");
    echo '<p>Error: Campos requeridos faltantes.</p>';
    exit;
}

// Validate user type
if (!SecurityHelper::validateUserType($_POST['tipo'])) {
    header("refresh:3;url=usuario_listado.php");
    echo '<p>Error: Tipo de usuario inválido.</p>';
    exit;
}

// Validate username format
if (!SecurityHelper::validateUsername($_POST['usuario'])) {
    header("refresh:3;url=usuario_listado.php");
    echo '<p>Error: Nombre de usuario inválido. Solo se permiten letras, números, guiones y guiones bajos.</p>';
    exit;
}

// Validate user ID is integer
$id = SecurityHelper::validateInteger($_POST['id']);
if (!$id) {
    header("refresh:3;url=usuario_listado.php");
    echo '<p>Error: ID de usuario inválido.</p>';
    exit;
}

// Check authorization
if (!SecurityHelper::authorize('edit_user', $id)) {
    SecurityHelper::logSecurityEvent('Unauthorized user update attempt for user ID: ' . $id, 'error');
    header("refresh:3;url=usuario_listado.php");
    echo '<p>Error: No tienes permiso para editar este usuario.</p>';
    exit;
}

$usuario = $_POST['usuario'];
$tipo = $_POST['tipo'];
$clave = null;
$foto_nombre = '';

$cnn = conectar();
if (!$cnn) {
    header("refresh:3;url=usuario_listado.php");
    echo '<p>Error: No se pudo conectar a la base de datos.</p>';
    exit;
}

// Handle password update
if (!empty($_POST['pass'])) {
    // Hash password with bcrypt
    $clave = SecurityHelper::hashPassword($_POST['pass']);
    
    if (!$clave) {
        desconectar($cnn);
        header("refresh:3;url=usuario_listado.php");
        echo '<p>Error: Contraseña inválida.</p>';
        exit;
    }
} else {
    // Keep existing password - fetch from database
    $sql = 'SELECT pass FROM usuario WHERE id_usuario = ?';
    $sentencia = mysqli_prepare($cnn, $sql);
    mysqli_stmt_bind_param($sentencia, 'i', $id);
    mysqli_stmt_execute($sentencia);
    mysqli_stmt_bind_result($sentencia, $clave);
    mysqli_stmt_fetch($sentencia);
}

// Handle file upload
if (!empty($_FILES['foto']['size'])) {
    // Validate file upload
    $validation = SecurityHelper::validateFileUpload($_FILES['foto']);
    
    if (!$validation['valid']) {
        desconectar($cnn);
        header("refresh:3;url=usuario_listado.php");
        echo '<p>Error: ' . htmlspecialchars($validation['error'], ENT_QUOTES, 'UTF-8') . '</p>';
        exit;
    }
    
    // Sanitize filename
    $foto_nombre = SecurityHelper::sanitizeFilename($_FILES['foto']['name'], $usuario);
    
    if (!$foto_nombre) {
        desconectar($cnn);
        header("refresh:3;url=usuario_listado.php");
        echo '<p>Error: Nombre de archivo inválido.</p>';
        exit;
    }
    
    // Move uploaded file
    $rutaO = $_FILES['foto']['tmp_name'];
    $destino = '../img/usuarios/' . $foto_nombre;
    
    if (!move_uploaded_file($rutaO, $destino)) {
        desconectar($cnn);
        header("refresh:3;url=usuario_listado.php");
        echo '<p>Error: No se pudo guardar la foto.</p>';
        SecurityHelper::logSecurityEvent('Failed to upload file for user: ' . $usuario, 'error');
        exit;
    }
}

// Update user in database
if (!empty($foto_nombre)) {
    $sql = 'UPDATE usuario SET usuario = ?, pass = ?, tipo = ?, foto = ? WHERE id_usuario = ?';
    $sentencia = mysqli_prepare($cnn, $sql);
    mysqli_stmt_bind_param($sentencia, 'ssssi', $usuario, $clave, $tipo, $foto_nombre, $id);
} else {
    $sql = 'UPDATE usuario SET usuario = ?, pass = ?, tipo = ? WHERE id_usuario = ?';
    $sentencia = mysqli_prepare($cnn, $sql);
    mysqli_stmt_bind_param($sentencia, 'sssi', $usuario, $clave, $tipo, $id);
}

$resultado = mysqli_stmt_execute($sentencia);

if ($resultado) {
    SecurityHelper::logSecurityEvent('User updated successfully: ' . $usuario, 'info');
    desconectar($cnn);
    header("refresh:3;url=usuario_listado.php");
    echo '<p>Usuario actualizado exitosamente.</p>';
} else {
    SecurityHelper::logSecurityEvent('User update failed for: ' . $usuario, 'error');
    desconectar($cnn);
    header("refresh:3;url=usuario_listado.php");
    echo '<p>Error al guardar datos.</p>';
}

?>