<?php
/**
 * BloodLife — Fetch Request Responses API Endpoint
 * Returns JSON array of donor responses for a given request ID.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/helpers/SecurityHelper.php';
require_once __DIR__ . '/../../app/helpers/SessionHelper.php';
require_once __DIR__ . '/../../app/models/BloodRequest.php';
require_once __DIR__ . '/../../app/models/RequestResponse.php';

SessionHelper::startSession();

if (!SessionHelper::isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

$userId = SessionHelper::getUserId();
$requestId = (int)($_GET['request_id'] ?? 0);

if ($requestId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request ID.']);
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
        echo json_encode(['success' => false, 'message' => 'Permission denied.']);
        exit;
    }

    $responses = RequestResponse::getResponsesByRequestId($requestId);

    echo json_encode([
        'success'   => true,
        'responses' => $responses
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error.']);
}
