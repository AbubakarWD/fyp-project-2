<?php
/**
 * BloodLife — Donation Controller
 * Handles viewing and logging verified blood donation history records.
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/SecurityHelper.php';
require_once __DIR__ . '/../helpers/SessionHelper.php';
require_once __DIR__ . '/../helpers/FormatHelper.php';
require_once __DIR__ . '/../models/DonationRecord.php';
require_once __DIR__ . '/../models/UserProfile.php';

class DonationController {

    /**
     * Display donor's verified donation history.
     */
    public static function index(): void {
        SessionHelper::startSession();
        if (!SessionHelper::isLoggedIn()) {
            SessionHelper::setFlash('warning', 'Please log in to view your donation history.');
            header("Location: " . APP_URL . "/login.php");
            exit;
        }

        $userId = SessionHelper::getUserId();
        $profile = UserProfile::findByUserId($userId);
        $donations = DonationRecord::getByDonorId($userId);

        $pageTitle = "My Donation History";
        require_once ROOT_PATH . '/views/donations/index.php';
    }

    /**
     * Log a new verified donation entry.
     */
    public static function store(): void {
        SessionHelper::startSession();
        if (!SessionHelper::isLoggedIn()) {
            header("Location: " . APP_URL . "/login.php");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . APP_URL . "/donation_history.php");
            exit;
        }

        if (!SecurityHelper::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            SessionHelper::setFlash('danger', 'Invalid security token.');
            header("Location: " . APP_URL . "/donation_history.php");
            exit;
        }

        $userId       = SessionHelper::getUserId();
        $facilityName = trim($_POST['facility_name'] ?? '');
        $city         = trim($_POST['city'] ?? '');
        $donationDate = trim($_POST['donation_date'] ?? '');
        $unitsDonated = (int)($_POST['units_donated'] ?? 1);
        $notes        = trim($_POST['notes'] ?? '');

        if (empty($facilityName) || empty($city) || empty($donationDate)) {
            SessionHelper::setFlash('danger', 'Facility Name, City, and Donation Date are required.');
            header("Location: " . APP_URL . "/donation_history.php");
            exit;
        }

        try {
            DonationRecord::create([
                'donor_id'      => $userId,
                'request_id'    => null,
                'donation_date' => $donationDate,
                'facility_name' => $facilityName,
                'city'          => $city,
                'units_donated' => $unitsDonated,
                'notes'         => $notes
            ]);

            SessionHelper::setFlash('success', 'Donation record added to your history successfully!');
        } catch (Exception $e) {
            SessionHelper::setFlash('danger', 'Error adding donation record: ' . $e->getMessage());
        }

        header("Location: " . APP_URL . "/donation_history.php");
        exit;
    }
}
