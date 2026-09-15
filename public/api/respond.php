<?php
/**
 * BloodLife — Donor Response Submission API Endpoint
 * Handles donor responses ("I Can Donate") to emergency blood requests.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/helpers/SecurityHelper.php';
require_once __DIR__ . '/../../app/helpers/SessionHelper.php';
require_once __DIR__ . '/../../app/models/BloodRequest.php';
require_once __DIR__ . '/../../app/models/RequestResponse.php';
require_once __DIR__ . '/../../app/models/Notification.php';
require_once __DIR__ . '/../../app/models/UserProfile.php';

SessionHelper::startSession();

if (!SessionHelper::isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access. Please log in.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed. Use POST.']);
    exit;
}

$donorId = SessionHelper::getUserId();
$requestId = (int)($_POST['request_id'] ?? 0);
$unitsOffered = max(1, (int)($_POST['units_offered'] ?? 1));
$donorNote = SecurityHelper::sanitizeString($_POST['donor_note'] ?? '');

if ($requestId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid blood request ID.']);
    exit;
}

try {
    $request = BloodRequest::findById($requestId);
    if (!$request) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Blood request not found.']);
        exit;
    }

    if ((int)$request['requester_id'] === $donorId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'You cannot respond to your own blood request.']);
        exit;
    }

    $responseId = RequestResponse::create($requestId, $donorId, $unitsOffered, $donorNote);

    // Notify Requester
    $donorProfile = UserProfile::findByUserId($donorId);
    $donorName = $donorProfile['full_name'] ?? 'A voluntary donor';
    
    Notification::create(
        (int)$request['requester_id'],
        'new_response',
        'New Donor Response Received',
        $donorName . ' responded to your blood request for ' . $request['patient_name'] . '.',
        '/requests.php?id=' . $requestId
    );

    echo json_encode([
        'success'     => true,
        'response_id' => $responseId,
        'message'     => 'Thank you! Your response has been sent to the requester.'
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
