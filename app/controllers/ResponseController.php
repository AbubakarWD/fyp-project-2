<?php
/**
 * BloodLife — Response Controller
 * Manages donor responses, connection management, status transitions, and conversation dispatch.
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/SecurityHelper.php';
require_once __DIR__ . '/../helpers/SessionHelper.php';
require_once __DIR__ . '/../helpers/FormatHelper.php';
require_once __DIR__ . '/../models/BloodRequest.php';
require_once __DIR__ . '/../models/RequestResponse.php';
require_once __DIR__ . '/../models/DonationRecord.php';
require_once __DIR__ . '/../models/Notification.php';
require_once __DIR__ . '/../models/UserProfile.php';

class ResponseController {

    /**
     * Render Donor/Requester Responses & Connection Management Page.
     */
    public static function index(): void {
        SessionHelper::startSession();
        if (!SessionHelper::isLoggedIn()) {
            SessionHelper::setFlash('warning', 'Please log in to access your response connection hub.');
            header("Location: " . APP_URL . "/login.php");
            exit;
        }

        $userId = SessionHelper::getUserId();
        $userRole = SessionHelper::getRole();

        $mySubmittedResponses = [];
        $receivedResponses = [];

        if ($userRole === 'donor' || $userRole === 'both') {
            $mySubmittedResponses = RequestResponse::getResponsesByDonorId($userId);
        }

        if ($userRole === 'requester' || $userRole === 'both') {
            // Fetch all responses across all requests owned by this user
            $myRequests = BloodRequest::getAll(['requester_id' => $userId], 50, 0);
            foreach ($myRequests as $req) {
                $reqResponses = RequestResponse::getResponsesByRequestId($req['id']);
                foreach ($reqResponses as $resp) {
                    $resp['patient_name'] = $req['patient_name'];
                    $resp['hospital_name'] = $req['hospital_name'];
                    $receivedResponses[] = $resp;
                }
            }
        }

        $pageTitle = "My Responses & Connections";
        require_once ROOT_PATH . '/views/responses/index.php';
    }
}
