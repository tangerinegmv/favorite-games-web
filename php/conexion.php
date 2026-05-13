<?php

/**
 * Secure Database Connection
 * 
 * UPDATED for security:
 * - Loads credentials from .env (not hardcoded)
 * - Proper error handling
 * - UTF-8 charset enforcement
 * 
 * @author Security Team
 * @version 2.0.0
 */

// Load environment variables
require_once dirname(__DIR__) . '/config/Environment.php';
$env = Environment::load();

function conectar()
{
    global $env;
    
    $servidor = $env['DB_HOST'] ?? 'localhost';
    $usuario = $env['DB_USER'] ?? 'root';
    $clave = $env['DB_PASS'] ?? '';
    $bd = $env['DB_NAME'] ?? 'labo2';

    try {
        $conexion = @mysqli_connect($servidor, $usuario, $clave, $bd);
        
        if (!$conexion) {
            throw new Exception('Connection failed');
        }
        
        // Set UTF-8 charset
        mysqli_set_charset($conexion, 'utf8mb4');
        
        return $conexion;
    } catch (Exception $e) {
        // Log error but don't expose details to user
        error_log('Database connection error: ' . $e->getMessage());
        return false;
    }
}

function desconectar($conexion) 
{
    if ($conexion) {
        mysqli_close($conexion);
    }
}

?>