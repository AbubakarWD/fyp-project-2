<?php
/**
 * BloodLife — Donor Controller
 * Search, filter, and pagination engine for voluntary blood donor discovery.
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/SecurityHelper.php';
require_once __DIR__ . '/../helpers/SessionHelper.php';
require_once __DIR__ . '/../helpers/FormatHelper.php';
require_once __DIR__ . '/../models/BloodGroup.php';
require_once __DIR__ . '/../models/UserProfile.php';

class DonorController {

    /**
     * Render Donor Discovery & Search Page.
     */
    public static function index(): void {
        SessionHelper::startSession();

        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 12;
        $offset = ($page - 1) * $limit;

        $bloodGroupCode = strtoupper(trim($_GET['blood_group'] ?? ''));
        $city = trim($_GET['city'] ?? '');
        $search = trim($_GET['search'] ?? '');
        $isAvailable = isset($_GET['is_available_donor']) ? $_GET['is_available_donor'] : '';

        // Resolve blood group code to ID if needed
        $bloodGroupId = (int)($_GET['blood_group_id'] ?? 0);
        if ($bloodGroupId <= 0 && !empty($bloodGroupCode)) {
            $bgRecord = BloodGroup::findByCode($bloodGroupCode);
            if ($bgRecord) {
                $bloodGroupId = (int)$bgRecord['id'];
            }
        }

        $filters = [
            'blood_group_id'     => $bloodGroupId,
            'city'               => $city,
            'is_available_donor' => $isAvailable,
            'search'             => $search
        ];

        // Fetch Donors & Count
        $donors = UserProfile::searchDonors($filters, $limit, $offset);
        $totalDonors = UserProfile::countDonors($filters);
        $totalPages = max(1, (int)ceil($totalDonors / $limit));

        $allBloodGroups = BloodGroup::getAll();

        $pageTitle = "Find Voluntary Blood Donors";
        require_once ROOT_PATH . '/views/donors/index.php';
    }
}
