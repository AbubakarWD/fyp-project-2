<?php
/**
 * BloodLife — Phase 5 Donor Dashboard Test Suite
 */

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/helpers/SessionHelper.php';
require_once __DIR__ . '/../app/models/User.php';
require_once __DIR__ . '/../app/models/UserProfile.php';
require_once __DIR__ . '/../app/models/BloodRequest.php';
require_once __DIR__ . '/../app/models/RequestResponse.php';

SessionHelper::startSession();

echo "========================================================\n";
echo "BloodLife — Phase 5 Donor Dashboard Test Suite\n";
echo "========================================================\n";

$passCount = 0;
$failCount = 0;

function assertDonorTest(bool $condition, string $testName): void {
    global $passCount, $failCount;
    if ($condition) {
        echo "[PASS] " . $testName . "\n";
        $passCount++;
    } else {
        echo "[FAIL] " . $testName . "\n";
        $failCount++;
    }
}

// 1. Test Donor User Retrieval & Profile Completion Math
$donorUser = User::findByEmail('donor1@bloodlife.org');
assertDonorTest($donorUser !== null, "Donor user retrieval");

$donorProfile = UserProfile::findByUserId($donorUser['id']);
assertDonorTest($donorProfile !== null, "Donor profile retrieval");

// 2. Test Availability Toggle API Logic
$originalStatus = (int)($donorProfile['is_available_donor'] ?? 1);
$newStatus = $originalStatus === 1 ? 0 : 1;

$toggleResult = UserProfile::toggleAvailability($donorUser['id'], (bool)$newStatus);
assertDonorTest($toggleResult === true, "UserProfile::toggleAvailability() executed");

$updatedProfile = UserProfile::findByUserId($donorUser['id']);
assertDonorTest((int)$updatedProfile['is_available_donor'] === $newStatus, "Donor availability toggled in database");

// Restore original status
UserProfile::toggleAvailability($donorUser['id'], (bool)$originalStatus);

// 3. Test Matching Blood Requests Query for Donor
$matchingRequests = BloodRequest::getAll(['status' => 'active'], 10, 0);
assertDonorTest(is_array($matchingRequests), "BloodRequest::getAll() returns active requests array");

// 4. Test Donor Response Submissions List
$responses = RequestResponse::getResponsesByDonorId($donorUser['id']);
assertDonorTest(is_array($responses), "RequestResponse::getResponsesByDonorId() returns responses array");

echo "========================================================\n";
echo "SUMMARY: Passed: {$passCount} | Failed: {$failCount}\n";
echo "========================================================\n";

if ($failCount > 0) {
    exit(1);
}
