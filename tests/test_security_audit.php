<?php
/**
 * BloodLife — Phase 12 Security Audit & Hardening Test Suite
 */

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/helpers/SecurityHelper.php';
require_once __DIR__ . '/../app/helpers/SessionHelper.php';
require_once __DIR__ . '/../app/models/UserProfile.php';
require_once __DIR__ . '/../app/models/BloodGroup.php';

echo "========================================================\n";
echo "BloodLife — Phase 12 Security Audit & Hardening Test Suite\n";
echo "========================================================\n";

$passed = 0;
$failed = 0;

function assertTest(bool $condition, string $description): void {
    global $passed, $failed;
    if ($condition) {
        echo "[PASS] {$description}\n";
        $passed++;
    } else {
        echo "[FAIL] {$description}\n";
        $failed++;
    }
}

try {
    // 1. CSRF Generation & Validation
    $token = SecurityHelper::generateCsrfToken();
    assertTest(!empty($token) && strlen($token) === 64, "Generated 64-character hexadecimal CSRF token");

    $valid = SecurityHelper::verifyCsrfToken($token);
    assertTest($valid, "Valid CSRF token passes verification");

    $invalid = SecurityHelper::verifyCsrfToken('invalid_fake_token_123');
    assertTest(!$invalid, "Invalid CSRF token fails verification");

    $emptyToken = SecurityHelper::verifyCsrfToken('');
    assertTest(!$emptyToken, "Empty CSRF token fails verification");

    // 2. XSS Output Escaping
    $maliciousInput = '<script>alert("XSS Attack!");</script>';
    $escaped = SecurityHelper::e($maliciousInput);
    assertTest(strpos($escaped, '<script>') === false && strpos($escaped, '&lt;script&gt;') !== false, "XSS script tags escaped properly");

    $maliciousAttr = '" onmouseover="alert(1)"';
    $escapedAttr = SecurityHelper::e($maliciousAttr);
    assertTest(strpos($escapedAttr, '&quot;') !== false, "XSS attribute quotes escaped properly");

    // 3. SQL Injection Prevention
    $sqlInjStr = "' OR '1'='1' --";
    $searchResult = UserProfile::searchDonors(['search' => $sqlInjStr]);
    assertTest(is_array($searchResult), "SQL Injection attempt in search returns safe array without crashing or dumping all records");

    $bgResult = BloodGroup::findByCode($sqlInjStr);
    assertTest($bgResult === null, "SQL Injection attempt in findByCode returns null safely");

    // 4. Password Security (BCRYPT)
    $plainPassword = 'SecurePassword123!';
    $hash = SecurityHelper::hashPassword($plainPassword);
    assertTest(strpos($hash, '$2y$') === 0 || strpos($hash, '$2b$') === 0, "Password hashed with BCRYPT format");
    assertTest(SecurityHelper::verifyPassword($plainPassword, $hash), "Valid password matches BCRYPT hash");
    assertTest(!SecurityHelper::verifyPassword('WrongPassword', $hash), "Invalid password rejected against BCRYPT hash");

    // 5. Session Guard Verification
    SessionHelper::startSession();
    SessionHelper::logout();
    assertTest(!SessionHelper::isLoggedIn(), "SessionHelper::isLoggedIn returns false after logout");
    assertTest(SessionHelper::getUserId() === null, "SessionHelper::getUserId returns null after logout");

} catch (Exception $e) {
    echo "[FAIL] Unexpected Exception: " . $e->getMessage() . "\n";
    $failed++;
}

echo "========================================================\n";
echo "SUMMARY: Passed: {$passed} | Failed: {$failed}\n";
echo "========================================================\n";
exit($failed === 0 ? 0 : 1);
