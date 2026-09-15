<?php
/**
 * BloodLife — Phase 6 Recipient Dashboard Test Suite
 */

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/helpers/SessionHelper.php';
require_once __DIR__ . '/../app/models/User.php';
require_once __DIR__ . '/../app/models/BloodRequest.php';
require_once __DIR__ . '/../app/models/RequestResponse.php';
require_once __DIR__ . '/../app/models/DonationRecord.php';
require_once __DIR__ . '/../app/models/Notification.php';
require_once __DIR__ . '/../app/services/MatchingService.php';

SessionHelper::startSession();

echo "========================================================\n";
echo "BloodLife — Phase 6 Recipient Dashboard Test Suite\n";
echo "========================================================\n";

$passCount = 0;
$failCount = 0;

function assertRecipientTest(bool $condition, string $testName): void {
    global $passCount, $failCount;
    if ($condition) {
        echo "[PASS] " . $testName . "\n";
        $passCount++;
    } else {
        echo "[FAIL] " . $testName . "\n";
        $failCount++;
    }
}

// 1. Retrieve Requester User (Sarah Jenkins, ID=2)
$requesterUser = User::findByEmail('requester1@bloodlife.org');
assertRecipientTest($requesterUser !== null, "Requester user retrieval");

// 2. Create Test Blood Request for Requester
$reqData = [
    'requester_id'     => $requesterUser['id'],
    'patient_name'     => 'Phase 6 Test Patient',
    'blood_group_id'   => 7, // O+
    'units_required'   => 2,
    'urgency_level'    => 'critical',
    'hospital_name'    => 'Aga Khan Hospital',
    'hospital_address' => 'Stadium Road',
    'city'             => 'Karachi',
    'contact_number'   => '+923001234567',
    'required_date'    => date('Y-m-d', strtotime('+2 days')),
    'medical_reason'   => 'Urgent surgery test'
];

$requestId = BloodRequest::create($reqData);
assertRecipientTest($requestId > 0, "BloodRequest::create() for requester");

// 3. Test Automated Donor Notification Dispatch
$notified = MatchingService::notifyMatchingDonors($requestId);
assertRecipientTest($notified >= 0, "MatchingService::notifyMatchingDonors() executed");

// 4. Test Donor Response Submission (John Doe, ID=1)
$responseId = RequestResponse::create($requestId, 1, 1, 'Can donate 1 unit O+');
assertRecipientTest($responseId > 0, "Donor response created for request");

// 5. Test Requester Accept & Complete Action
$acceptResult = RequestResponse::updateStatus($responseId, 'accepted');
assertRecipientTest($acceptResult === true, "RequestResponse marked as 'accepted'");

$completeResult = RequestResponse::updateStatus($responseId, 'completed');
assertRecipientTest($completeResult === true, "RequestResponse marked as 'completed'");

$fulfilledUpdate = BloodRequest::updateFulfilledUnits($requestId, 1);
assertRecipientTest($fulfilledUpdate === true, "BloodRequest units_fulfilled updated");

// 6. Test Request Cancellation
$cancelResult = BloodRequest::updateStatus($requestId, 'cancelled');
assertRecipientTest($cancelResult === true, "BloodRequest marked as 'cancelled'");

echo "========================================================\n";
echo "SUMMARY: Passed: {$passCount} | Failed: {$failCount}\n";
echo "========================================================\n";

if ($failCount > 0) {
    exit(1);
}
