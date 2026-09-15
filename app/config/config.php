<?php
/**
 * BloodLife — Application Configuration
 * Centralized configuration settings for the BloodLife Web Application.
 */

// Application Info
define('APP_NAME', 'BloodLife');
define('APP_TAGLINE', 'Social Blood Donation & Emergency Assistance Platform');
define('APP_VERSION', '1.0.0');
define('APP_URL', getenv('APP_URL') ?: 'http://127.0.0.1:8000');

// Directory Paths
define('ROOT_PATH', dirname(__DIR__, 2));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('VIEWS_PATH', ROOT_PATH . '/views');
define('UPLOAD_PATH', PUBLIC_PATH . '/uploads');

// Database Credentials
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'consultant_db');
define('DB_USER', getenv('DB_USER') ?: 'consultant_user');
define('DB_PASS', getenv('DB_PASS') ?: 'consunt001Site');
define('DB_CHARSET', 'utf8mb4');

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

