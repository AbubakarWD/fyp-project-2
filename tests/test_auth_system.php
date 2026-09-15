<?php
/**
 * BloodLife — Phase 3 Authentication Test Suite
 */

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/helpers/SecurityHelper.php';
require_once __DIR__ . '/../app/helpers/SessionHelper.php';
require_once __DIR__ . '/../app/models/User.php';

SessionHelper::startSession();

echo "========================================================\n";
echo "BloodLife — Phase 3 Authentication Test Suite\n";
echo "========================================================\n";

$passCount = 0;
$failCount = 0;

function assertTest(bool $condition, string $testName): void {
    global $passCount, $failCount;
    if ($condition) {
        echo "[PASS] " . $testName . "\n";
        $passCount++;
    } else {
        echo "[FAIL] " . $testName . "\n";
        $failCount++;
    }
}

// 1. Test Donor Valid Credentials Search & Password Verification
$donorUser = User::findByEmail('donor1@bloodlife.org');
assertTest($donorUser !== null, "Donor account search by email");
assertTest($donorUser && $donorUser['primary_role'] === 'donor', "Donor role check");
assertTest($donorUser && SecurityHelper::verifyPassword('Password123!', $donorUser['password']), "Donor valid BCRYPT password verification");

// 2. Test Invalid Password Verification
assertTest(!$donorUser || !SecurityHelper::verifyPassword('WrongPassword!', $donorUser['password']), "Invalid password verification fails");

// 3. Test Requester Account & Role
$requesterUser = User::findByEmail('requester1@bloodlife.org');
assertTest($requesterUser !== null && $requesterUser['primary_role'] === 'requester', "Requester account search and role check");

// 4. Test Registration with New User Data
$newEmail = 'testuser_' . time() . '@bloodlife.org';
$testRegData = [
    'full_name'      => 'Test User',
    'email'          => $newEmail,
    'phone_number'   => '+923000000000',
    'blood_group_id' => 1, // A+
    'city'           => 'Karachi',
    'primary_role'   => 'donor',
    'username'       => 'testuser_' . time(),
    'password_hash'  => SecurityHelper::hashPassword('Password123!')
];

try {
    $newUserId = User::register($testRegData);
    assertTest($newUserId > 0, "New user registration database insertion");

    $createdUser = User::findById($newUserId);
    assertTest($createdUser !== null && $createdUser['email'] === $newEmail, "Retrieve newly created user profile");
    assertTest($createdUser['blood_group'] === 'A+', "Blood group association check (A+)");

    // 5. Test Duplicate Email Registration Guard
    $duplicateUser = User::findByEmail($newEmail);
    assertTest($duplicateUser !== null, "Duplicate email check query returns existing record");

} catch (Exception $e) {
    assertTest(false, "New user registration failed: " . $e->getMessage());
}

// 6. Test Session Login & Role Helper
SessionHelper::login($donorUser);
assertTest(SessionHelper::isLoggedIn() === true, "SessionHelper::isLoggedIn() returns true after login");
assertTest(SessionHelper::getUserId() === (int)$donorUser['id'], "SessionHelper::getUserId() matches logged-in user");
assertTest(SessionHelper::isDonor() === true, "SessionHelper::isDonor() returns true for donor user");

// 7. Test Session Logout
SessionHelper::logout();
assertTest(SessionHelper::isLoggedIn() === false, "SessionHelper::isLoggedIn() returns false after logout");

echo "========================================================\n";
echo "SUMMARY: Passed: {$passCount} | Failed: {$failCount}\n";
echo "========================================================\n";

if ($failCount > 0) {
    exit(1);
}
