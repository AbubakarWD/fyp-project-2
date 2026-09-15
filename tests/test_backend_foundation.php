<?php
/**
 * BloodLife — Phase 4 Backend Foundation Test Suite
 */

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/helpers/SessionHelper.php';
require_once __DIR__ . '/../app/models/BloodGroup.php';
require_once __DIR__ . '/../app/models/BloodRequest.php';
require_once __DIR__ . '/../app/models/RequestResponse.php';
require_once __DIR__ . '/../app/models/DonationRecord.php';
require_once __DIR__ . '/../app/models/Notification.php';
require_once __DIR__ . '/../app/models/UserProfile.php';
require_once __DIR__ . '/../app/services/MatchingService.php';

SessionHelper::startSession();

echo "========================================================\n";
echo "BloodLife — Phase 4 Backend Foundation Test Suite\n";
echo "========================================================\n";

$passCount = 0;
$failCount = 0;

function assertBackendTest(bool $condition, string $testName): void {
    global $passCount, $failCount;
    if ($condition) {
        echo "[PASS] " . $testName . "\n";
        $passCount++;
    } else {
        echo "[FAIL] " . $testName . "\n";
        $failCount++;
    }
}

// 1. Test BloodGroup Master Table Data & ABO Compatibility
$allBloodGroups = BloodGroup::getAll();
assertBackendTest(count($allBloodGroups) === 8, "BloodGroup::getAll() returns 8 groups");

$oNegGroup = BloodGroup::findByCode('O-');
assertBackendTest($oNegGroup !== null && $oNegGroup['code'] === 'O-', "BloodGroup::findByCode('O-')");

$compatibleDonorIdsForA = BloodGroup::getCompatibleDonorGroupIds('A+');
assertBackendTest(!empty($compatibleDonorIdsForA), "BloodGroup::getCompatibleDonorGroupIds('A+') returns donor group IDs");

// 2. Test BloodRequest Creation & Querying
$testRequestData = [
    'requester_id'     => 2, // Sarah Jenkins
    'patient_name'     => 'Unit Test Patient',
    'blood_group_id'   => 1, // A+
    'units_required'   => 2,
    'urgency_level'    => 'high',
    'hospital_name'    => 'Test Memorial Hospital',
    'hospital_address' => '123 Test Street',
    'city'             => 'Karachi',
    'contact_number'   => '+923001112233',
    'required_date'    => date('Y-m-d', strtotime('+3 days')),
    'medical_reason'   => 'Automated test request'
];

try {
    $requestId = BloodRequest::create($testRequestData);
    assertBackendTest($requestId > 0, "BloodRequest::create() creates record");

    $request = BloodRequest::findById($requestId);
    assertBackendTest($request !== null && $request['patient_name'] === 'Unit Test Patient', "BloodRequest::findById() retrieves created record");
    assertBackendTest($request['blood_group'] === 'A+', "BloodRequest JOIN returns blood_group 'A+'");

    // 3. Test MatchingService Donor Matching & Notification Dispatch
    $matchingDonors = MatchingService::findMatchingDonors('A+', 'Karachi', true);
    assertBackendTest(is_array($matchingDonors), "MatchingService::findMatchingDonors() executes query");

    $notifiedCount = MatchingService::notifyMatchingDonors($requestId);
    assertBackendTest($notifiedCount >= 0, "MatchingService::notifyMatchingDonors() dispatches notifications");

    // 4. Test Donor Response Creation & Duplicate Guard
    $responseId = RequestResponse::create($requestId, 1, 1, 'I can donate 1 unit in Karachi');
    assertBackendTest($responseId > 0, "RequestResponse::create() creates donor response");

    try {
        RequestResponse::create($requestId, 1, 1, 'Duplicate response test');
        assertBackendTest(false, "Duplicate response guard should throw exception");
    } catch (Exception $e) {
        assertBackendTest(true, "Duplicate response guard prevents duplicate response from same donor");
    }

    // 5. Test Status Transitions
    $statusUpdate = RequestResponse::updateStatus($responseId, 'accepted');
    assertBackendTest($statusUpdate === true, "RequestResponse::updateStatus() updates to 'accepted'");

    $fulfilledUpdate = BloodRequest::updateFulfilledUnits($requestId, 1);
    assertBackendTest($fulfilledUpdate === true, "BloodRequest::updateFulfilledUnits() updates partially fulfilled units");

    // 6. Test Donation Record Creation & Donation Count Increment
    $initialProfile = UserProfile::findByUserId(1);
    $initialCount = (int)($initialProfile['total_donations_count'] ?? 0);

    $donationData = [
        'donor_id'      => 1,
        'request_id'    => $requestId,
        'donation_date' => date('Y-m-d'),
        'facility_name' => 'Test Memorial Hospital',
        'city'          => 'Karachi',
        'units_donated' => 1,
        'notes'         => 'Test donation completed'
    ];

    $donationId = DonationRecord::create($donationData);
    assertBackendTest($donationId > 0, "DonationRecord::create() inserts record");

    $updatedProfile = UserProfile::findByUserId(1);
    $updatedCount = (int)($updatedProfile['total_donations_count'] ?? 0);
    assertBackendTest($updatedCount === ($initialCount + 1), "UserProfile total_donations_count incremented after donation");

    // 7. Test Notification Retrieval & Unread Count
    $notifications = Notification::getByUserId(1);
    assertBackendTest(is_array($notifications), "Notification::getByUserId() retrieves user notifications");

    $unreadCount = Notification::getUnreadCount(1);
    assertBackendTest($unreadCount >= 0, "Notification::getUnreadCount() returns unread count");

    // 8. Test Donor Search Filters
    $donors = UserProfile::searchDonors(['city' => 'Karachi']);
    assertBackendTest(count($donors) > 0, "UserProfile::searchDonors(['city' => 'Karachi']) returns donors");

} catch (Exception $e) {
    assertBackendTest(false, "Backend foundation test failed with exception: " . $e->getMessage());
}

echo "========================================================\n";
echo "SUMMARY: Passed: {$passCount} | Failed: {$failCount}\n";
echo "========================================================\n";

if ($failCount > 0) {
    exit(1);
}
