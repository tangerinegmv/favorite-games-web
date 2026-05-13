

<?php

/**
 * SecurityHelper Class
 * 
 * Comprehensive security utilities for the Favorite Games Web application.
 * Implements best practices for:
 * - Password hashing (bcrypt)
 * - CSRF protection
 * - Input validation
 * - File upload security
 * - Authorization checks
 * - Security logging
 * 
 * @author Security Team
 * @version 1.0.0
 */

class SecurityHelper
{
    private static $uploadDir = '../img/usuarios/';
    private static $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    private static $maxFileSize = 5242880; // 5MB in bytes
    private static $logDir = '../logs/';

    /**
     * Hash a password using bcrypt algorithm
     * 
     * @param string $password Plain text password
     * @return string|false Hashed password or false on failure
     */
    public static function hashPassword($password)
    {
        if (empty($password) || strlen($password) < 6) {
            return false;
        }
        
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Verify a password against a bcrypt hash
     * 
     * @param string $password Plain text password to verify
     * @param string $hash The bcrypt hash to compare against
     * @return bool True if password matches hash
     */
    public static function verifyPassword($password, $hash)
    {
        if (empty($password) || empty($hash)) {
            return false;
        }
        
        return password_verify($password, $hash);
    }

    /**
     * Generate a CSRF token for form protection
     * 
     * @return string The generated CSRF token
     */
    public static function generateCSRFToken()
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Validate a CSRF token from a form submission
     * 
     * @param string $token The token to validate
     * @return bool True if token is valid
     */
    public static function validateCSRFToken($token)
    {
        if (empty($token) || empty($_SESSION['csrf_token'])) {
            return false;
        }
        
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Validate username format
     * 
     * @param string $username Username to validate
     * @return bool True if valid username format
     */
    public static function validateUsername($username)
    {
        if (empty($username) || strlen($username) > 45) {
            return false;
        }
        
        // Allow alphanumeric, underscore, hyphen (no special chars that could cause issues)
        return preg_match('/^[a-zA-Z0-9_-]+$/', $username) === 1;
    }

    /**
     * Validate email format
     * 
     * @param string $email Email to validate
     * @return bool True if valid email format
     */
    public static function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate integer input
     * 
     * @param mixed $value Value to validate
     * @return int|false Integer value or false if not valid
     */
    public static function validateInteger($value)
    {
        if (!is_numeric($value)) {
            return false;
        }
        
        $int = (int)$value;
        return ($int > 0) ? $int : false;
    }

    /**
     * Validate user type (Administrador or Común)
     * 
     * @param string $type User type to validate
     * @return bool True if valid user type
     */
    public static function validateUserType($type)
    {
        $validTypes = ['Administrador', 'Común'];
        return in_array($type, $validTypes, true);
    }

    /**
     * Validate file upload and check for security issues
     * 
     * @param array $file $_FILES array for the uploaded file
     * @param array $allowedMimes Optional: array of allowed MIME types
     * @param int $maxSize Optional: maximum file size in bytes
     * @return array Array with 'valid' => bool and 'error' => string (if invalid)
     */
    public static function validateFileUpload($file, $allowedMimes = null, $maxSize = null)
    {
        $allowedMimes = $allowedMimes ?? self::$allowedMimes;
        $maxSize = $maxSize ?? self::$maxFileSize;

        // Check if file exists in array
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['valid' => false, 'error' => 'File upload error'];
        }

        // Check file size
        if ($file['size'] > $maxSize) {
            return ['valid' => false, 'error' => 'File size exceeds maximum allowed'];
        }

        // Validate MIME type using finfo (not user input)
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if (!in_array($mimeType, $allowedMimes, true)) {
            return ['valid' => false, 'error' => 'Invalid file type'];
        }

        return ['valid' => true];
    }

    /**
     * Sanitize filename for safe storage
     * 
     * @param string $filename Original filename
     * @param string $prefix Optional: prefix to add (usually username)
     * @return string Safe filename
     */
    public static function sanitizeFilename($filename, $prefix = '')
    {
        // Get file extension
        $pathinfo = pathinfo($filename);
        $extension = strtolower($pathinfo['extension']);
        
        // Validate extension
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($extension, $allowedExtensions, true)) {
            return false;
        }

        // Create safe filename
        if (!empty($prefix)) {
            return preg_replace('/[^a-zA-Z0-9_-]/', '', $prefix) . '.' . $extension;
        }
        
        return preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
    }

    /**
     * Check authorization for resource access
     * 
     * @param string $action Action to authorize (e.g., 'edit_user', 'delete_user')
     * @param mixed $resourceOwnerId ID of the resource owner
     * @param mixed $currentUserId Optional: ID of current user (defaults to session user)
     * @return bool True if authorized
     */
    public static function authorize($action, $resourceOwnerId, $currentUserId = null)
    {
        // Ensure user is authenticated
        if (!isset($_SESSION['usuario'])) {
            return false;
        }

        // Get current user ID (you'll need to add this to session during login)
        if ($currentUserId === null) {
            $currentUserId = $_SESSION['user_id'] ?? null;
        }

        if ($currentUserId === null) {
            return false;
        }

        // Check authorization based on action and resource owner
        switch ($action) {
            case 'edit_user':
            case 'view_profile':
                // Users can edit their own profile or admins can edit anyone
                return ($currentUserId == $resourceOwnerId) || $_SESSION['user_type'] === 'Administrador';
            
            case 'delete_user':
            case 'deactivate_user':
                // Only admins can delete/deactivate
                return $_SESSION['user_type'] === 'Administrador';
            
            case 'admin_action':
                // Only admins
                return $_SESSION['user_type'] === 'Administrador';
            
            default:
                return false;
        }
    }

    /**
     * Initialize secure session configuration
     * 
     * @return void
     */
    public static function initSecureSession()
    {
        // Only set session options if session hasn't started
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.use_strict_mode', 1);
            ini_set('session.use_only_cookies', 1);
            session_set_cookie_params([
                'lifetime' => 3600,        // 1 hour
                'path' => '/',
                'domain' => '',
                'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
                'httponly' => true,        // Prevent JavaScript access
                'samesite' => 'Strict'     // CSRF protection
            ]);
            
            session_start();
        }
    }

    /**
     * Regenerate session ID after login (prevent session fixation)
     * 
     * @return void
     */
    public static function regenerateSession()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        session_regenerate_id(true);
    }

    /**
     * Validate session is active and secure
     * 
     * @return bool True if session is valid
     */
    public static function validateSession()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (empty($_SESSION['usuario']) || empty($_SESSION['user_id']) || empty($_SESSION['user_type'])) {
            return false;
        }

        if (empty($_SESSION['fingerprint']) || $_SESSION['fingerprint'] !== self::getSessionFingerprint()) {
            return false;
        }

        // Optional timeout: 30 minutes of inactivity
        if (!empty($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > 1800) {
            return false;
        }

        $_SESSION['last_activity'] = time();
        return true;
    }

    /**
     * Create a fingerprint for the current session
     * 
     * @return string
     */
    public static function getSessionFingerprint()
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $ipSegment = 'unknown';

        if (!empty($_SERVER['REMOTE_ADDR'])) {
            $parts = explode('.', $_SERVER['REMOTE_ADDR']);
            $ipSegment = count($parts) >= 2 ? ($parts[0] . '.' . $parts[1]) : $_SERVER['REMOTE_ADDR'];
        }

        return hash('sha256', $userAgent . '|' . $ipSegment);
    }

    /**
     * Require a logged-in session or redirect to login
     *
     * @return void
     */
    public static function requireLogin()
    {
        if (!self::validateSession()) {
            header('Location: ../index.php');
            exit;
        }
    }

    /**
     * Require administrator privileges or show error
     *
     * @return void
     */
    public static function requireAdmin()
    {
        if (!self::validateSession() || ($_SESSION['user_type'] ?? '') !== 'Administrador') {
            header('refresh:3;url=usuario_listado.php');
            echo '<p>Error: No tienes permiso para realizar esta acción.</p>';
            exit;
        }
    }

    /**
     * Sanitize HTML output to prevent XSS
     * 
     * @param string $input User input to sanitize
     * @return string Sanitized output safe for HTML
     */
    public static function sanitizeHTML($input)
    {
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Log security events for audit trail
     * 
     * @param string $event Event description
     * @param string $level Log level (info, warning, error)
     * @param array $context Optional: additional context data
     * @return bool True if logged successfully
     */
    public static function logSecurityEvent($event, $level = 'info', $context = [])
    {
        // Create logs directory if it doesn't exist
        if (!is_dir(self::$logDir)) {
            mkdir(self::$logDir, 0755, true);
        }

        $logFile = self::$logDir . 'security-' . date('Y-m-d') . '.log';
        $timestamp = date('Y-m-d H:i:s');
        $user = $_SESSION['usuario'] ?? 'ANONYMOUS';
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';

        $logEntry = sprintf(
            "[%s] [%s] User: %s | IP: %s | Event: %s",
            $timestamp,
            strtoupper($level),
            $user,
            $ip,
            $event
        );

        if (!empty($context)) {
            $logEntry .= ' | Context: ' . json_encode($context);
        }

        $logEntry .= PHP_EOL;

        return file_put_contents($logFile, $logEntry, FILE_APPEND) !== false;
    }

    /**
     * Rate limiting check to prevent brute force
     * 
     * @param string $identifier Unique identifier (usually username or IP)
     * @param int $maxAttempts Maximum attempts allowed
     * @param int $timeWindow Time window in seconds
     * @return bool True if within limits
     */
    public static function rateLimit($identifier, $maxAttempts = 5, $timeWindow = 300)
    {
        $cacheKey = 'rate_limit_' . md5($identifier);
        
        if (!isset($_SESSION[$cacheKey])) {
            $_SESSION[$cacheKey] = [
                'attempts' => 0,
                'first_attempt' => time()
            ];
        }

        $record = $_SESSION[$cacheKey];
        $timeElapsed = time() - $record['first_attempt'];

        // Reset if outside time window
        if ($timeElapsed > $timeWindow) {
            $_SESSION[$cacheKey] = [
                'attempts' => 1,
                'first_attempt' => time()
            ];
            return true;
        }

        // Check if within limits
        if ($record['attempts'] >= $maxAttempts) {
            return false;
        }

        // Increment attempts
        $_SESSION[$cacheKey]['attempts']++;
        return true;
    }

    /**
     * Destroy session safely
     * 
     * @return void
     */
    public static function destroySession()
    {
        $_SESSION = [];
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        
        session_destroy();
    }
}

?>