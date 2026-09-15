<?php
/**
 * BloodLife — Dashboard Controller
 * Fetches metrics, matching requests, and activity logs for Donor and Recipient dashboards.
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/SecurityHelper.php';
require_once __DIR__ . '/../helpers/SessionHelper.php';
require_once __DIR__ . '/../helpers/FormatHelper.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/UserProfile.php';
require_once __DIR__ . '/../models/BloodRequest.php';
require_once __DIR__ . '/../models/RequestResponse.php';
require_once __DIR__ . '/../models/DonationRecord.php';
require_once __DIR__ . '/../models/Notification.php';
require_once __DIR__ . '/../services/MatchingService.php';

class DashboardController {

    /**
     * Render Donor Dashboard.
     */
    public static function donorDashboard(array $currentUser): void {
        $userId = (int)($currentUser['id'] ?? $currentUser['user_id'] ?? 0);
        $userProfile = UserProfile::findByUserId($userId);

        // Calculate Profile Completion %
        $completionFields = ['full_name', 'phone_number', 'blood_group_id', 'city', 'address', 'bio', 'last_donated_at'];
        $filledCount = 0;
        foreach ($completionFields as $field) {
            if (!empty($userProfile[$field])) {
                $filledCount++;
            }
        }
        $profileCompletion = round(($filledCount / count($completionFields)) * 100);

        // Fetch Donor Metrics
        $totalDonations = (int)($userProfile['total_donations_count'] ?? 0);
        $responses = RequestResponse::getResponsesByDonorId($userId);
        $respondedCount = count($responses);
        $acceptedCount = 0;
        foreach ($responses as $resp) {
            if ($resp['status'] === 'accepted' || $resp['status'] === 'completed') {
                $acceptedCount++;
            }
        }

        // Fetch Matching Blood Requests
        $matchingRequests = [];
        if (!empty($userProfile['blood_group'])) {
            $matchingRequests = BloodRequest::getAll([
                'status' => 'active'
            ], 10, 0);

            // Filter for ABO compatibility
            $matchingRequests = array_filter($matchingRequests, function($req) use ($userProfile) {
                return FormatHelper::isCompatible($userProfile['blood_group'], $req['blood_group']);
            });
        }

        // Fetch Recent Notifications / Activity
        $recentNotifications = Notification::getByUserId($userId, 5);

        $pageTitle = "Donor Dashboard";
        require_once ROOT_PATH . '/views/dashboard/donor_dashboard.php';
    }

    /**
     * Render Recipient / Requester Dashboard.
     */
    public static function recipientDashboard(array $currentUser): void {
        $userId = (int)($currentUser['id'] ?? $currentUser['user_id'] ?? 0);
        $userProfile = UserProfile::findByUserId($userId);

        // Fetch Requester Metrics
        $myRequests = BloodRequest::getAll(['requester_id' => $userId], 20, 0);
        
        $activeRequestsCount = 0;
        $fulfilledRequestsCount = 0;
        foreach ($myRequests as $req) {
            if ($req['status'] === 'active' || $req['status'] === 'partially_fulfilled') {
                $activeRequestsCount++;
            } elseif ($req['status'] === 'fulfilled') {
                $fulfilledRequestsCount++;
            }
        }

        // Total responses received for my requests
        $totalResponsesReceived = 0;
        foreach ($myRequests as $req) {
            $totalResponsesReceived += (int)($req['response_count'] ?? 0);
        }

        // Count available donors in city
        $availableDonorsCount = UserProfile::countDonors([
            'city' => $userProfile['city'] ?? '',
            'is_available_donor' => 1
        ]);

        $recentNotifications = Notification::getByUserId($userId, 5);

        $pageTitle = "Recipient Dashboard";
        require_once ROOT_PATH . '/views/dashboard/recipient_dashboard.php';
    }
}
