<?php
/**
 * BloodLife — Profile Controller
 * Manages user profile updates, availability toggling, and avatar uploads.
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/SecurityHelper.php';
require_once __DIR__ . '/../helpers/SessionHelper.php';
require_once __DIR__ . '/../helpers/FormatHelper.php';
require_once __DIR__ . '/../models/UserProfile.php';
require_once __DIR__ . '/../models/BloodGroup.php';
require_once __DIR__ . '/../services/FileUploadService.php';

class ProfileController {

    /**
     * Render Profile View & Edit Page.
     */
    public static function index(): void {
        SessionHelper::startSession();
        if (!SessionHelper::isLoggedIn()) {
            SessionHelper::setFlash('warning', 'Please log in to manage your profile.');
            header("Location: " . APP_URL . "/login.php");
            exit;
        }

        $userId = SessionHelper::getUserId();
        $profile = UserProfile::findByUserId($userId);
        $bloodGroups = BloodGroup::getAll();

        $pageTitle = "My Profile & Preferences";
        require_once ROOT_PATH . '/views/profile/index.php';
    }

    /**
     * Process Profile Updates.
     */
    public static function update(): void {
        SessionHelper::startSession();
        if (!SessionHelper::isLoggedIn()) {
            header("Location: " . APP_URL . "/login.php");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . APP_URL . "/profile.php");
            exit;
        }

        if (!SecurityHelper::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            SessionHelper::setFlash('danger', 'Invalid security token. Please try again.');
            header("Location: " . APP_URL . "/profile.php");
            exit;
        }

        $userId = SessionHelper::getUserId();
        $fullName     = trim($_POST['full_name'] ?? '');
        $phoneNumber  = trim($_POST['phone_number'] ?? '');
        $gender       = trim($_POST['gender'] ?? 'other');
        $bloodGroupId = (int)($_POST['blood_group_id'] ?? 0);
        $city         = trim($_POST['city'] ?? '');
        $address      = trim($_POST['address'] ?? '');
        $bio          = trim($_POST['bio'] ?? '');
        $isAvailable  = isset($_POST['is_available_donor']) ? 1 : 0;

        $errors = [];
        if (empty($fullName)) {
            $errors[] = "Full Name is required.";
        }
        if (empty($phoneNumber)) {
            $errors[] = "Phone Number is required.";
        }
        if ($bloodGroupId <= 0) {
            $errors[] = "Please select a valid blood group.";
        }
        if (empty($city)) {
            $errors[] = "City is required.";
        }

        if (!empty($errors)) {
            SessionHelper::setFlash('danger', implode('<br>', $errors));
            header("Location: " . APP_URL . "/profile.php");
            exit;
        }

        $updated = UserProfile::update($userId, [
            'full_name'          => $fullName,
            'phone_number'       => $phoneNumber,
            'gender'             => $gender,
            'blood_group_id'     => $bloodGroupId,
            'city'               => $city,
            'address'            => $address,
            'bio'                => $bio,
            'is_available_donor' => $isAvailable
        ]);

        if ($updated) {
            SessionHelper::setFlash('success', 'Your profile has been updated successfully!');
        } else {
            SessionHelper::setFlash('danger', 'Failed to update profile. Please try again.');
        }

        header("Location: " . APP_URL . "/profile.php");
        exit;
    }

    /**
     * Process Avatar Image Upload.
     */
    public static function uploadAvatar(): void {
        SessionHelper::startSession();
        if (!SessionHelper::isLoggedIn()) {
            header("Location: " . APP_URL . "/login.php");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . APP_URL . "/profile.php");
            exit;
        }

        if (!SecurityHelper::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            SessionHelper::setFlash('danger', 'Invalid security token.');
            header("Location: " . APP_URL . "/profile.php");
            exit;
        }

        if (empty($_FILES['avatar']['name'])) {
            SessionHelper::setFlash('warning', 'Please select an image to upload.');
            header("Location: " . APP_URL . "/profile.php");
            exit;
        }

        try {
            $userId = SessionHelper::getUserId();
            $newFilename = FileUploadService::uploadAvatar($_FILES['avatar']);
            UserProfile::updateAvatar($userId, $newFilename);
            SessionHelper::setFlash('success', 'Profile picture updated successfully!');
        } catch (Exception $e) {
            SessionHelper::setFlash('danger', $e->getMessage());
        }

        header("Location: " . APP_URL . "/profile.php");
        exit;
    }
}
