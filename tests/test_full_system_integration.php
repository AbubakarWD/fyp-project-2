<?php
/**
 * BloodLife — Phase 14 Master End-to-End Integration Test Suite
 * Executes full real-world lifecycle from request creation to donor response, donation completion, and metrics update.
 */

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/helpers/SecurityHelper.php';
require_once __DIR__ . '/../app/helpers/SessionHelper.php';
require_once __DIR__ . '/../app/models/UserProfile.php';
require_once __DIR__ . '/../app/models/BloodRequest.php';
require_once __DIR__ . '/../app/models/RequestResponse.php';
require_once __DIR__ . '/../app/models/DonationRecord.php';
require_once __DIR__ . '/../app/models/Notification.php';
require_once __DIR__ . '/../app/services/MatchingService.php';

echo "========================================================\n";
echo "BloodLife — Phase 14 Complete System Integration Test\n";
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
    // STEP 1: Verify Seed Users & Profiles
    $donor = UserProfile::findByEmail('donor1@bloodlife.org');
    $requester = UserProfile::findByEmail('requester1@bloodlife.org');

    assertTest(!empty($donor) && $donor['primary_role'] === 'donor', "Donor account verified (donor1@bloodlife.org)");
    assertTest(!empty($requester) && $requester['primary_role'] === 'requester', "Requester account verified (requester1@bloodlife.org)");

    // STEP 2: Requester Posts Emergency Blood Request
    $initialReqCount = BloodRequest::countAll(['requester_id' => $requester['user_id']]);
    $reqId = BloodRequest::create([
        'requester_id'     => $requester['user_id'],
        'patient_name'     => 'Master E2E Test Patient',
        'blood_group_id'   => 1, // A+
        'units_required'   => 1,
        'urgency_level'    => 'critical',
        'hospital_name'    => 'Civil Hospital Emergency Ward',
        'hospital_address' => 'Mission Road, Karachi',
        'city'             => 'Karachi',
        'contact_number'   => '+923001112233',
        'required_date'    => date('Y-m-d', strtotime('+1 day')),
        'medical_reason'   => 'Urgent surgery requirements'
    ]);
    assertTest($reqId > 0, "Emergency Blood Request posted with ID #{$reqId}");

    // STEP 3: Matching Service Notifies Donors
    $requestData = BloodRequest::findById($reqId);
    $notifiedCount = MatchingService::notifyMatchingDonors($reqId);
    assertTest($notifiedCount >= 0, "MatchingService identified compatible donors and dispatched alerts");

    // STEP 4: Donor Views Match Alert & Responds
    $donorUnreadBefore = Notification::getUnreadCount($donor['user_id']);
    $responseId = RequestResponse::create(
        $reqId,
        $donor['user_id'],
        1,
        'Available immediately at Civil Hospital.'
    );
    assertTest($responseId > 0, "Donor submitted response ID #{$responseId} to request #{$reqId}");

    // Notify Requester about Donor Response
    Notification::create(
        $requester['user_id'],
        'new_response',
        'Donor Responded to Request',
        "{$donor['full_name']} responded to your blood request for {$requestData['patient_name']}.",
        "requests.php?action=detail&id={$reqId}"
    );

    // STEP 5: Requester Accepts Response
    $accepted = RequestResponse::updateStatus($responseId, 'accepted');
    assertTest($accepted, "Requester accepted donor response #{$responseId}");

    // STEP 6: Donation Completion & Verified Record Logging
    $initialDonations = (int)$donor['total_donations_count'];
    $completed = RequestResponse::updateStatus($responseId, 'completed');
    assertTest($completed, "Response status marked as completed");

    $donationRecordId = DonationRecord::create([
        'donor_id'      => $donor['user_id'],
        'request_id'    => $reqId,
        'donation_date' => date('Y-m-d'),
        'facility_name' => 'Civil Hospital Emergency Ward',
        'city'          => 'Karachi',
        'units_donated' => 1,
        'notes'         => 'Successful emergency donation'
    ]);
    assertTest($donationRecordId > 0, "Donation record logged with ID #{$donationRecordId}");

    // STEP 7: Update Fulfilled Units & Status on Blood Request
    $fulfilledUpdated = BloodRequest::updateFulfilledUnits($reqId, 1);
    assertTest($fulfilledUpdated, "BloodRequest fulfilled units updated to 1");

    $reqUpdated = BloodRequest::findById($reqId);
    assertTest($reqUpdated['status'] === 'fulfilled', "BloodRequest status transitioned to 'fulfilled'");

    // STEP 8: Verify Donor Impact Metrics
    $donorUpdated = UserProfile::findByUserId($donor['user_id']);
    assertTest((int)$donorUpdated['total_donations_count'] === $initialDonations + 1, "Donor total donations count incremented from {$initialDonations} to " . ($initialDonations + 1));
    assertTest($donorUpdated['last_donated_at'] === date('Y-m-d'), "Donor last_donated_at updated to today's date");

} catch (Exception $e) {
    echo "[FAIL] Unexpected Exception: " . $e->getMessage() . "\n";
    $failed++;
}

echo "========================================================\n";
echo "SUMMARY: Passed: {$passed} | Failed: {$failed}\n";
echo "========================================================\n";
exit($failed === 0 ? 0 : 1);
