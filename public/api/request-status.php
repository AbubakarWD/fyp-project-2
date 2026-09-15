<?php
/**
 * BloodLife — Request Status Update API Endpoint
 * Allows requesters to mark blood requests as cancelled or fulfilled.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/helpers/SecurityHelper.php';
require_once __DIR__ . '/../../app/helpers/SessionHelper.php';
require_once __DIR__ . '/../../app/models/BloodRequest.php';

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

$userId = SessionHelper::getUserId();
$requestId = (int)($_POST['request_id'] ?? 0);
$status = $_POST['status'] ?? '';

$allowedStatuses = ['cancelled', 'fulfilled'];
if ($requestId <= 0 || !in_array($status, $allowedStatuses, true)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid parameters provided.']);
    exit;
}

try {
    $request = BloodRequest::findById($requestId);
    if (!$request) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Request not found.']);
        exit;
    }

    if ((int)$request['requester_id'] !== $userId) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Permission denied. You can only manage your own requests.']);
        exit;
    }

    $success = BloodRequest::updateStatus($requestId, $status);
    
    if ($success) {
        echo json_encode([
            'success' => true,
            'status'  => $status,
            'message' => 'Request status updated to: ' . ucfirst($status)
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update request status.']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error.']);
}
