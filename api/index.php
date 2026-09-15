<?php
/**
 * BloodLife — Vercel Serverless PHP Adapter Entrypoint
 * Bootstraps and routes incoming Vercel requests to the core public application scripts.
 */

// Establish ROOT_PATH and bootstrap session save path for serverless environment
define('VERCEL_SERVERLESS', true);
$root = dirname(__DIR__);

// Ensure writable session storage directory in Vercel serverless environment (/tmp)
$tmpDir = sys_get_temp_dir();
if (is_dir($tmpDir) && is_writable($tmpDir)) {
    @session_save_path($tmpDir);
}

// Extract requested URI path
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$parsedUrl = parse_url($requestUri);
$path = rawurldecode($parsedUrl['path'] ?? '/');

// Clean up trailing slashes and normalize path
if ($path === '/' || $path === '/index.php' || $path === '') {
    $targetScript = $root . '/public/index.php';
} else {
    // Strip leading slash
    $relative = ltrim($path, '/');
    
    // Check if target file exists inside public/ or public/api/
    $publicTarget = $root . '/public/' . $relative;
    
    if (file_exists($publicTarget) && is_file($publicTarget) && str_ends_with($publicTarget, '.php')) {
        $targetScript = $publicTarget;
    } elseif (file_exists($publicTarget . '.php') && is_file($publicTarget . '.php')) {
        $targetScript = $publicTarget . '.php';
    } else {
        // Fallback to main public index
        $targetScript = $root . '/public/index.php';
    }
}

// Security: Prevent Directory Traversal outside /public
$realTarget = realpath($targetScript);
$realPublic = realpath($root . '/public');

if ($realTarget && $realPublic && str_starts_with($realTarget, $realPublic)) {
    // Set working directory to public for consistent relative includes inside public scripts
    chdir($realPublic);
    require $realTarget;
} else {
    http_response_code(404);
    require $root . '/views/errors/404.php';
}
