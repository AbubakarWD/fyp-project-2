<?php
/**
 * BloodLife — Donor Response Status Action API Endpoint
 * Requesters can accept/reject responses or mark donations completed.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/helpers/SecurityHelper.php';
require_once __DIR__ . '/../../app/helpers/SessionHelper.php';
require_once __DIR__ . '/../../app/models/BloodRequest.php';
require_once __DIR__ . '/../../app/models/RequestResponse.php';
require_once __DIR__ . '/../../app/models/DonationRecord.php';
require_once __DIR__ . '/../../app/models/Notification.php';

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
$responseId = (int)($_POST['response_id'] ?? 0);
$status = $_POST['status'] ?? '';

$allowedStatuses = ['accepted', 'rejected', 'completed'];
if ($responseId <= 0 || !in_array($status, $allowedStatuses, true)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid parameters provided.']);
    exit;
}

try {
    $response = RequestResponse::findById($responseId);
    if (!$response) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Donor response not found.']);
        exit;
    }

    if ((int)$response['requester_id'] !== $userId) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Permission denied. Only request owner can manage donor responses.']);
        exit;
    }

    $success = RequestResponse::updateStatus($responseId, $status);

    if ($success) {
        // If marked completed, increment fulfilled units and record donation log
        if ($status === 'completed') {
            $request = BloodRequest::findById($response['request_id']);
            $newUnits = ((int)$request['units_fulfilled']) + ((int)$response['units_offered']);
            BloodRequest::updateFulfilledUnits($response['request_id'], $newUnits);

            DonationRecord::create([
                'donor_id'      => $response['donor_id'],
                'request_id'    => $response['request_id'],
                'donation_date' => date('Y-m-d'),
                'facility_name' => $request['hospital_name'],
                'city'          => $request['city'],
                'units_donated' => (int)$response['units_offered'],
                'notes'         => 'Verified donation for patient ' . $request['patient_name']
            ]);

            // Notify Donor
            Notification::create(
                (int)$response['donor_id'],
                'request_update',
                'Donation Verified!',
                'Your donation for ' . $request['patient_name'] . ' has been verified. Thank you for saving a life!',
                '/donation_history.php'
            );
        } elseif ($status === 'accepted') {
            Notification::create(
                (int)$response['donor_id'],
                'request_update',
                'Response Accepted!',
                'The requester accepted your response for patient ' . $response['patient_name'] . '. Please coordinate logistics.',
                '/requests.php'
            );
        }

        echo json_encode([
            'success' => true,
            'status'  => $status,
            'message' => 'Donor response marked as: ' . ucfirst($status)
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update response status.']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
