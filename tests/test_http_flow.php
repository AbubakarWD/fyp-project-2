<?php
/**
 * BloodLife — HTTP Endpoint Runtime Verification Script
 */

require_once __DIR__ . '/../app/config/config.php';

function testLoginAndFetch(string $email, string $pass, array $paths): void {
    $cookieFile = sys_get_temp_dir() . '/cookie_' . md5($email) . '.txt';
    if (file_exists($cookieFile)) @unlink($cookieFile);

    // 1. Get CSRF token from login page
    $ch = curl_init('http://127.0.0.1:8000/login.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    $html = curl_exec($ch);
    curl_close($ch);

    preg_match('/name="csrf_token"\s+value="([^"]+)"/', $html, $m);
    $csrf = $m[1] ?? '';

    // 2. Post login credentials
    $ch = curl_init('http://127.0.0.1:8000/login.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'csrf_token' => $csrf,
        'email' => $email,
        'password' => $pass
    ]));
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "User {$email} Login => HTTP {$code}\n";

    // 3. Fetch authenticated routes
    foreach ($paths as $path) {
        $ch = curl_init('http://127.0.0.1:8000/' . $path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
        $pageHtml = curl_exec($ch);
        $pageCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        echo "   Fetch /{$path} => HTTP {$pageCode} (" . strlen($pageHtml) . " bytes)\n";
    }
}

echo "========================================================\n";
echo "BloodLife — HTTP Runtime Endpoint Audit\n";
echo "========================================================\n";

testLoginAndFetch('donor1@bloodlife.org', 'Password123!', ['dashboard.php', 'profile.php', 'donation_history.php', 'notifications.php', 'responses.php']);
testLoginAndFetch('requester1@bloodlife.org', 'Password123!', ['dashboard.php', 'request-create.php', 'requests.php']);

echo "========================================================\n";
