<?php
/**
 * SECURE Login Handler
 * 
 * Security features implemented:
 * - CSRF token validation
 * - Bcrypt password verification
 * - Rate limiting (5 attempts per 5 minutes)
 * - Session regeneration (prevent session fixation)
 * - Input validation
 * - Security logging
 * - Generic error messages (prevent user enumeration)
 * 
 * @author Security Team
 * @version 2.0.0
 */

require_once 'SecurityHelper.php';

// Initialize secure session
SecurityHelper::initSecureSession();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

// Validate CSRF token
if (!isset($_POST['csrf_token']) || !SecurityHelper::validateCSRFToken($_POST['csrf_token'])) {
    SecurityHelper::logSecurityEvent('CSRF token validation failed', 'warning');
    header('refresh:3;url=../index.php');
    die('<p>Error de seguridad: Token CSRF invalido. Intente de nuevo.</p>');
}

// Validate input presence
if (empty($_POST['usuario']) || empty($_POST['pass'])) {
    SecurityHelper::logSecurityEvent('Login attempt with empty credentials', 'warning');
    header('refresh:3;url=../index.php');
    die('<p>Usuario y contraseña son requeridos.</p>');
}

// Get credentials
$usuForm = $_POST['usuario'];
$claveForm = $_POST['pass'];

// Validate username format
if (!SecurityHelper::validateUsername($usuForm)) {
    SecurityHelper::logSecurityEvent('Login attempt with invalid username format', 'warning', ['username' => $usuForm]);
    header('refresh:3;url=../index.php');
    die('<p>Formato de usuario inválido.</p>');
}

// Rate limiting check
if (!SecurityHelper::rateLimit($usuForm, 5, 300)) {
    SecurityHelper::logSecurityEvent('Rate limit exceeded for user', 'error', ['username' => $usuForm]);
    header('refresh:5;url=../index.php');
    die('<p>Demasiados intentos de inicio de sesión. Intente nuevamente en 5 minutos.</p>');
}

// Connect to database
include 'conexion.php';
$cnn = conectar();

if (!$cnn) {
    SecurityHelper::logSecurityEvent('Database connection failed during login', 'error');
    header('refresh:3;url=../index.php');
    die('<p>Error en la conexión. Comuníquese con su Administrador.</p>');
}

try {
    // Query to get user info
    $sql = 'SELECT id_usuario, pass, foto FROM usuario WHERE usuario = ? AND activado = \'S\'';
    $sentencia = mysqli_prepare($cnn, $sql);
    
    if (!$sentencia) {
        throw new Exception('Prepare error: ' . mysqli_error($cnn));
    }
    
    mysqli_stmt_bind_param($sentencia, 's', $usuForm);
    
    if (!mysqli_stmt_execute($sentencia)) {
        throw new Exception('Execute error: ' . mysqli_error($cnn));
    }
    
    mysqli_stmt_bind_result($sentencia, $id_usuario, $hashPassword, $foto);
    mysqli_stmt_store_result($sentencia);
    $cantFilas = mysqli_stmt_num_rows($sentencia);
    
    // Generic error message (prevents user enumeration)
    $errorMessage = 'Usuario y contraseña no encontrados en la base de datos.';
    
    if ($cantFilas === 1) {
        mysqli_stmt_fetch($sentencia);
        
        // Verify password using bcrypt
        if (SecurityHelper::verifyPassword($claveForm, $hashPassword)) {
            // Password is correct - login success
            
            // Regenerate session ID to prevent session fixation
            SecurityHelper::regenerateSession();
            
            // Set session variables
            $_SESSION['usuario'] = $usuForm;
            $_SESSION['user_id'] = $id_usuario;
            $_SESSION['foto'] = $foto ?? 'usuario_default.png';
            
            // Log successful login
            SecurityHelper::logSecurityEvent('Successful login', 'info', ['user_id' => $id_usuario]);
            
            // Redirect to game list
            header('refresh:1;url=juego_listado.php');
            die('<p>Inicio de sesión exitoso. Redirigiendo...</p>');
        } else {
            // Password doesn't match
            SecurityHelper::logSecurityEvent('Failed login attempt - invalid password', 'warning', ['username' => $usuForm]);
        }
    } else {
        // User not found
        SecurityHelper::logSecurityEvent('Failed login attempt - user not found', 'warning', ['username' => $usuForm]);
    }
    
    // Generic error for both cases
    header('refresh:3;url=../index.php');
    die('<p>' . $errorMessage . '</p>');
    
} catch (Exception $e) {
    error_log('Login error: ' . $e->getMessage());
    SecurityHelper::logSecurityEvent('Login error: ' . $e->getMessage(), 'error');
    header('refresh:3;url=../index.php');
    die('<p>Error durante el inicio de sesión. Intente nuevamente.</p>');
} finally {
    if (isset($sentencia)) {
        mysqli_stmt_close($sentencia);
    }
    desconectar($cnn);
}

?>