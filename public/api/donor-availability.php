<?php
/**
 * BloodLife — Donor Availability Toggle API Endpoint
 * Handles AJAX requests to switch donor availability status in real time.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/helpers/SecurityHelper.php';
require_once __DIR__ . '/../../app/helpers/SessionHelper.php';
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

$userId = SessionHelper::getUserId();
$isAvailable = isset($_POST['is_available']) ? (int)$_POST['is_available'] : 1;

try {
    $success = UserProfile::toggleAvailability($userId, (bool)$isAvailable);
    
    if ($success) {
        $statusText = $isAvailable ? 'Available to donate' : 'Currently unavailable';
        echo json_encode([
            'success'      => true,
            'is_available' => $isAvailable,
            'message'      => 'Your status is now: ' . $statusText
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update availability status.']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error occurred.']);
}
