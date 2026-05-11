<?php

/**
 * Environment Variable Loader
 * 
 * Safely loads and parses .env files
 * 
 * @author Security Team
 * @version 1.0.0
 */

class Environment
{
    private static $variables = [];
    private static $loaded = false;

    /**
     * Load .env file from repository root
     * 
     * @param string $path Optional: path to .env file
     * @return array Array of environment variables
     */
    public static function load($path = null)
    {
        if (self::$loaded) {
            return self::$variables;
        }

        if ($path === null) {
            $path = dirname(__DIR__) . '/.env';
        }

        if (!file_exists($path)) {
            error_log("Warning: .env file not found at $path");
            return [];
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Skip comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Parse KEY=VALUE
            if (strpos($line, '=') !== false) {
                [$key, $value] = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // Remove quotes if present
                if ((strpos($value, '"') === 0 && strpos($value, '"', 1) === strlen($value) - 1) ||
                    (strpos($value, "'") === 0 && strpos($value, "'", 1) === strlen($value) - 1)) {
                    $value = substr($value, 1, -1);
                }
                
                self::$variables[$key] = $value;
            }
        }

        self::$loaded = true;
        return self::$variables;
    }

    /**
     * Get environment variable
     * 
     * @param string $key Variable key
     * @param mixed $default Default value if not found
     * @return mixed Environment variable value or default
     */
    public static function get($key, $default = null)
    {
        if (!self::$loaded) {
            self::load();
        }

        return self::$variables[$key] ?? $default;
    }
}

?>