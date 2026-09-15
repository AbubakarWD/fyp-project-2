<?php
/**
 * BloodLife — Phase 8 Request Management Test Suite
 */

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/models/User.php';
require_once __DIR__ . '/../app/models/BloodRequest.php';
require_once __DIR__ . '/../app/models/RequestResponse.php';

echo "========================================================\n";
echo "BloodLife — Phase 8 Request Management Test Suite\n";
echo "========================================================\n";

$passCount = 0;
$failCount = 0;

function assertRequestMgmtTest(bool $condition, string $testName): void {
    global $passCount, $failCount;
    if ($condition) {
        echo "[PASS] " . $testName . "\n";
        $passCount++;
    } else {
        echo "[FAIL] " . $testName . "\n";
        $failCount++;
    }
}

// 1. Test Request Feed Search & Filtering
$activeRequests = BloodRequest::getAll(['status' => 'active'], 10, 0);
assertRequestMgmtTest(is_array($activeRequests), "BloodRequest::getAll(['status' => 'active']) returns array");

$karachiRequests = BloodRequest::getAll(['city' => 'Karachi'], 10, 0);
assertRequestMgmtTest(is_array($karachiRequests), "BloodRequest::getAll(['city' => 'Karachi']) returns array");

// 2. Test Request Detail Retrieval
$firstRequest = !empty($activeRequests) ? $activeRequests[0] : null;
assertRequestMgmtTest($firstRequest !== null, "Active request exists for detail testing");

if ($firstRequest) {
    $detail = BloodRequest::findById($firstRequest['id']);
    assertRequestMgmtTest($detail !== null && (int)$detail['id'] === (int)$firstRequest['id'], "BloodRequest::findById() retrieves full detail record");
    assertRequestMgmtTest(!empty($detail['requester_email']), "BloodRequest detail includes requester email JOIN");
}

// 3. Test Request Update Operation
if ($firstRequest) {
    $pdo = Database::getInstance();
    $stmt = $pdo->prepare("UPDATE blood_requests SET medical_reason = 'Updated in test suite' WHERE id = :id");
    $updateResult = $stmt->execute(['id' => $firstRequest['id']]);
    assertRequestMgmtTest($updateResult === true, "BloodRequest medical_reason updated");

    $updatedDetail = BloodRequest::findById($firstRequest['id']);
    assertRequestMgmtTest($updatedDetail['medical_reason'] === 'Updated in test suite', "Verified updated medical reason in database");
}

echo "========================================================\n";
echo "SUMMARY: Passed: {$passCount} | Failed: {$failCount}\n";
echo "========================================================\n";

if ($failCount > 0) {
    exit(1);
}
