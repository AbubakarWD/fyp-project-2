<?php
/**
 * BloodLife — Phase 7 Donor Discovery Test Suite
 */

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/models/BloodGroup.php';
require_once __DIR__ . '/../app/models/UserProfile.php';

echo "========================================================\n";
echo "BloodLife — Phase 7 Donor Discovery Test Suite\n";
echo "========================================================\n";

$passCount = 0;
$failCount = 0;

function assertDiscoveryTest(bool $condition, string $testName): void {
    global $passCount, $failCount;
    if ($condition) {
        echo "[PASS] " . $testName . "\n";
        $passCount++;
    } else {
        echo "[FAIL] " . $testName . "\n";
        $failCount++;
    }
}

// 1. Test All Donors Search Query
$allDonors = UserProfile::searchDonors([], 20, 0);
$totalCount = UserProfile::countDonors([]);
assertDiscoveryTest(count($allDonors) > 0, "UserProfile::searchDonors() returns non-empty result set");
assertDiscoveryTest($totalCount > 0, "UserProfile::countDonors() returns total count > 0");

// 2. Test City Filtering (Karachi)
$karachiDonors = UserProfile::searchDonors(['city' => 'Karachi'], 20, 0);
$karachiCount = UserProfile::countDonors(['city' => 'Karachi']);
assertDiscoveryTest(is_array($karachiDonors), "City filter 'Karachi' returns donors array");
foreach ($karachiDonors as $d) {
    assertDiscoveryTest($d['city'] === 'Karachi', "Donor city matches 'Karachi'");
}

// 3. Test Blood Group Filtering (O-)
$oNegGroup = BloodGroup::findByCode('O-');
if ($oNegGroup) {
    $oNegDonors = UserProfile::searchDonors(['blood_group_id' => $oNegGroup['id']], 20, 0);
    assertDiscoveryTest(is_array($oNegDonors), "Blood Group filter 'O-' executes query");
    foreach ($oNegDonors as $d) {
        assertDiscoveryTest($d['blood_group'] === 'O-', "Donor blood group matches 'O-'");
    }
}

// 4. Test Availability Status Filtering (is_available_donor = 1)
$availableDonors = UserProfile::searchDonors(['is_available_donor' => 1], 20, 0);
assertDiscoveryTest(is_array($availableDonors), "Availability filter returns available donors");
foreach ($availableDonors as $d) {
    assertDiscoveryTest((int)$d['is_available_donor'] === 1, "Donor availability flag is 1");
}

echo "========================================================\n";
echo "SUMMARY: Passed: {$passCount} | Failed: {$failCount}\n";
echo "========================================================\n";

if ($failCount > 0) {
    exit(1);
}
