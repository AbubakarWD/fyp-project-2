<?php
/**
 * BloodLife — Application Configuration
 * Centralized configuration settings for the BloodLife Web Application.
 */

// Load local .env file if available
$envFile = dirname(__DIR__, 2) . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $val) = explode('=', $line, 2);
            $key = trim($key);
            $val = trim($val, " \t\n\r\0\x0B\"'");
            putenv("{$key}={$val}");
            $_ENV[$key] = $val;
            $_SERVER[$key] = $val;
        }
    }
}

// Application Info
define('APP_NAME', 'BloodLife');
define('APP_TAGLINE', 'Social Blood Donation & Emergency Assistance Platform');
define('APP_VERSION', '1.0.0');

// Dynamically determine APP_URL if not explicitly set in environment
$envAppUrl = getenv('APP_URL') ?: ($_ENV['APP_URL'] ?? '');
if (empty($envAppUrl)) {
    if (isset($_SERVER['HTTP_HOST'])) {
        $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ? 'https' : 'http';
        $envAppUrl = $scheme . '://' . $_SERVER['HTTP_HOST'];
    } else {
        $envAppUrl = 'http://127.0.0.1:8000';
    }
}
define('APP_URL', rtrim($envAppUrl, '/'));

// Directory Paths
define('ROOT_PATH', dirname(__DIR__, 2));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('VIEWS_PATH', ROOT_PATH . '/views');
define('UPLOAD_PATH', PUBLIC_PATH . '/uploads');

// Database Credentials (loaded securely from environment variables)
define('DB_HOST', getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? '127.0.0.1'));
define('DB_PORT', getenv('DB_PORT') ?: ($_ENV['DB_PORT'] ?? '3306'));
define('DB_NAME', getenv('DB_NAME') ?: ($_ENV['DB_NAME'] ?? 'consultant_db'));
define('DB_USER', getenv('DB_USER') ?: ($_ENV['DB_USER'] ?? 'consultant_user'));
define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : ($_ENV['DB_PASS'] ?? ''));
define('DB_CHARSET', 'utf8mb4');
define('DB_SSL_CA', getenv('DB_SSL_CA') ?: ($_ENV['DB_SSL_CA'] ?? ''));

// Session & Security Config
define('SESSION_LIFETIME', 86400); // 24 hours
define('CSRF_TOKEN_KEY', 'bl_csrf_token');

// Timezone
date_default_timezone_set('Asia/Karachi');

// Security HTTP Response Headers
if (!headers_sent()) {
    header("X-Frame-Options: SAMEORIGIN");
    header("X-Content-Type-Options: nosniff");
    header("X-XSS-Protection: 1; mode=block");
    header("Referrer-Policy: strict-origin-when-cross-origin");
}

