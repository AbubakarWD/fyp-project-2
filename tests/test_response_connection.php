<?php
/**
 * BloodLife — Phase 9 Response & Connection Test Suite
 */

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/helpers/SessionHelper.php';
require_once __DIR__ . '/../app/models/UserProfile.php';
require_once __DIR__ . '/../app/models/BloodRequest.php';
require_once __DIR__ . '/../app/models/RequestResponse.php';

echo "========================================================\n";
echo "BloodLife — Phase 9 Response & Connection Test Suite\n";
echo "========================================================\n";

$db = Database::getInstance();
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
    // 1. Get test donor & requester
    $donor = UserProfile::findByEmail('donor1@bloodlife.org');
    $requester = UserProfile::findByEmail('requester1@bloodlife.org');
    assertTest(!empty($donor) && !empty($requester), "Donor and Requester accounts retrieved");

    // 2. Create a test blood request
    $reqData = [
        'requester_id' => $requester['id'],
        'patient_name' => 'Phase 9 Test Patient',
        'blood_group_id' => 1,
        'units_required' => 2,
        'urgency_level' => 'high',
        'hospital_name' => 'City General Hospital',
        'hospital_address' => '123 Health Ave',
        'city' => 'Karachi',
        'contact_number' => '+923009998877',
        'contact_email' => 'requester1@bloodlife.org',
        'required_date' => date('Y-m-d', strtotime('+3 days')),
        'medical_reason' => 'Surgical procedure test'
    ];
    $reqId = BloodRequest::create($reqData);
    assertTest($reqId > 0, "Created test blood request ID {$reqId}");

    // 3. Create donor response
    $responseId = RequestResponse::create(
        $reqId,
        $donor['id'],
        1,
        'I can donate tomorrow morning.'
    );
    assertTest($responseId > 0, "Created donor response ID {$responseId}");

    // 4. Verify duplicate response check
    $resp = RequestResponse::findByRequestAndDonor($reqId, $donor['id']);
    assertTest(!empty($resp) && $resp['status'] === 'pending', "Response retrieved with pending status");

    // 5. Update response status to accepted
    $updated = RequestResponse::updateStatus($resp['id'], 'accepted');
    assertTest($updated, "RequestResponse status updated to accepted");

    $respUpdated = RequestResponse::findById($resp['id']);
    assertTest($respUpdated['status'] === 'accepted', "Verified status is 'accepted'");

    // 6. Test cancellation by donor
    $cancelled = RequestResponse::updateStatus($resp['id'], 'cancelled');
    assertTest($cancelled, "RequestResponse status updated to cancelled");

    $respCancelled = RequestResponse::findById($resp['id']);
    assertTest($respCancelled['status'] === 'cancelled', "Verified status is 'cancelled'");

} catch (Exception $e) {
    echo "[FAIL] Unexpected Exception: " . $e->getMessage() . "\n";
    $failed++;
}

echo "========================================================\n";
echo "SUMMARY: Passed: {$passed} | Failed: {$failed}\n";
echo "========================================================\n";
exit($failed === 0 ? 0 : 1);
