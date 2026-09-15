<?php
/**
 * BloodLife — Donor Cancel Response API Endpoint
 * Allows a donor to cancel a pending response submitted to a blood request.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/helpers/SecurityHelper.php';
require_once __DIR__ . '/../../app/helpers/SessionHelper.php';
require_once __DIR__ . '/../../app/models/RequestResponse.php';

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
$responseId = (int)($_POST['response_id'] ?? 0);

if ($responseId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid response ID.']);
    exit;
}

try {
    $response = RequestResponse::findById($responseId);
    if (!$response) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Response not found.']);
        exit;
    }

    if ((int)$response['donor_id'] !== $donorId) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Permission denied. You can only manage your own responses.']);
        exit;
    }

    $success = RequestResponse::updateStatus($responseId, 'cancelled');

    if ($success) {
        echo json_encode([
            'success' => true,
            'message' => 'Your response has been cancelled.'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to cancel response.']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error.']);
}
