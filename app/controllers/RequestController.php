<?php
/**
 * BloodLife — Request Controller
 * Handles creation, editing, cancellation, status updates, detail view, and response tracking for blood requests.
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/SecurityHelper.php';
require_once __DIR__ . '/../helpers/SessionHelper.php';
require_once __DIR__ . '/../helpers/FormatHelper.php';
require_once __DIR__ . '/../models/BloodGroup.php';
require_once __DIR__ . '/../models/BloodRequest.php';
require_once __DIR__ . '/../models/RequestResponse.php';
require_once __DIR__ . '/../models/DonationRecord.php';
require_once __DIR__ . '/../models/Notification.php';
require_once __DIR__ . '/../models/UserProfile.php';
require_once __DIR__ . '/../services/MatchingService.php';

class RequestController {

    /**
     * Render Blood Requests Feed & Search Page.
     */
    public static function index(): void {
        SessionHelper::startSession();

        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 12;
        $offset = ($page - 1) * $limit;

        $bloodGroupCode = strtoupper(trim($_GET['blood_group'] ?? ''));
        $city = trim($_GET['city'] ?? '');
        $urgency = trim($_GET['urgency'] ?? '');
        $status = trim($_GET['status'] ?? 'active');

        $bloodGroupId = (int)($_GET['blood_group_id'] ?? 0);
        if ($bloodGroupId <= 0 && !empty($bloodGroupCode)) {
            $bgRecord = BloodGroup::findByCode($bloodGroupCode);
            if ($bgRecord) {
                $bloodGroupId = (int)$bgRecord['id'];
            }
        }

        $filters = [
            'blood_group_id' => $bloodGroupId,
            'city'           => $city,
            'urgency_level'  => $urgency,
            'status'         => $status
        ];

        $requests = BloodRequest::getAll($filters, $limit, $offset);
        $totalRequests = BloodRequest::countAll($filters);
        $totalPages = max(1, (int)ceil($totalRequests / $limit));

        $allBloodGroups = BloodGroup::getAll();

        $pageTitle = "Emergency Blood Requests";
        require_once ROOT_PATH . '/views/requests/index.php';
    }

    /**
     * Render Detailed View for an Individual Blood Request.
     */
    public static function detail(): void {
        SessionHelper::startSession();

        $requestId = (int)($_GET['id'] ?? 0);
        if ($requestId <= 0) {
            header("Location: " . APP_URL . "/requests.php");
            exit;
        }

        $request = BloodRequest::findById($requestId);
        if (!$request) {
            SessionHelper::setFlash('danger', 'Requested blood request was not found.');
            header("Location: " . APP_URL . "/requests.php");
            exit;
        }

        $userId = SessionHelper::getUserId();
        $isOwner = ($userId && (int)$request['requester_id'] === $userId);

        // Fetch Donor Responses if Owner
        $responses = [];
        if ($isOwner) {
            $responses = RequestResponse::getResponsesByRequestId($requestId);
        }

        // Check if current logged-in donor already responded
        $userResponse = null;
        if ($userId && !$isOwner) {
            $userResponse = RequestResponse::findByRequestAndDonor($requestId, $userId);
        }

        $pageTitle = "Blood Request #" . $requestId . " — " . $request['patient_name'];
        require_once ROOT_PATH . '/views/requests/detail.php';
    }

    /**
     * Render Blood Request Creation Form & Handle Submission.
     */
    public static function create(): void {
        SessionHelper::startSession();
        if (!SessionHelper::isLoggedIn()) {
            SessionHelper::setFlash('warning', 'Please log in to submit a blood request.');
            header("Location: " . APP_URL . "/login.php");
            exit;
        }

        $userId = SessionHelper::getUserId();
        $errors = [];
        $formData = [
            'patient_name'     => '',
            'blood_group_id'   => '',
            'units_required'   => 1,
            'urgency_level'    => 'medium',
            'hospital_name'    => '',
            'hospital_address' => '',
            'city'             => '',
            'contact_number'   => '',
            'required_date'    => date('Y-m-d', strtotime('+1 day')),
            'medical_reason'   => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!SecurityHelper::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                $errors[] = "Security token expired. Please reload and try again.";
            }

            $formData['patient_name']     = SecurityHelper::sanitizeString($_POST['patient_name'] ?? '');
            $formData['blood_group_id']   = (int)($_POST['blood_group_id'] ?? 0);
            $formData['units_required']   = max(1, min(10, (int)($_POST['units_required'] ?? 1)));
            $formData['urgency_level']    = $_POST['urgency_level'] ?? 'medium';
            $formData['hospital_name']    = SecurityHelper::sanitizeString($_POST['hospital_name'] ?? '');
            $formData['hospital_address'] = SecurityHelper::sanitizeString($_POST['hospital_address'] ?? '');
            $formData['city']             = SecurityHelper::sanitizeString($_POST['city'] ?? '');
            $formData['contact_number']   = SecurityHelper::sanitizeString($_POST['contact_number'] ?? '');
            $formData['required_date']    = $_POST['required_date'] ?? date('Y-m-d');
            $formData['medical_reason']   = SecurityHelper::sanitizeString($_POST['medical_reason'] ?? '');

            // Validation Checks
            if (empty($formData['patient_name'])) {
                $errors[] = "Patient Name is required.";
            }
            if ($formData['blood_group_id'] <= 0) {
                $errors[] = "Please select the required Blood Group.";
            }
            if (empty($formData['hospital_name'])) {
                $errors[] = "Hospital Name is required.";
            }
            if (empty($formData['city'])) {
                $errors[] = "Please select the City.";
            }
            if (empty($formData['contact_number'])) {
                $errors[] = "Contact Phone Number is required.";
            }
            if (empty($formData['required_date'])) {
                $errors[] = "Required Date is required.";
            }

            if (empty($errors)) {
                try {
                    $requestData = array_merge($formData, [
                        'requester_id' => $userId
                    ]);

                    $requestId = BloodRequest::create($requestData);

                    // Dispatch notifications to compatible local donors
                    $notifiedCount = MatchingService::notifyMatchingDonors($requestId);

                    SessionHelper::setFlash(
                        'success', 
                        'Blood request created successfully! ' . $notifiedCount . ' matching donor(s) were notified.'
                    );

                    header("Location: " . APP_URL . "/dashboard.php");
                    exit;

                } catch (Exception $e) {
                    $errors[] = "Failed to create blood request: " . $e->getMessage();
                }
            }
        }

        $pageTitle = "Create Blood Request";
        require_once ROOT_PATH . '/views/requests/create.php';
    }

    /**
     * Render Blood Request Edit Form & Handle Updates.
     */
    public static function edit(): void {
        SessionHelper::startSession();
        if (!SessionHelper::isLoggedIn()) {
            SessionHelper::setFlash('warning', 'Please log in to edit your request.');
            header("Location: " . APP_URL . "/login.php");
            exit;
        }

        $userId = SessionHelper::getUserId();
        $requestId = (int)($_GET['id'] ?? 0);
        $request = BloodRequest::findById($requestId);

        if (!$request || (int)$request['requester_id'] !== $userId) {
            SessionHelper::setFlash('danger', 'You do not have permission to edit this request.');
            header("Location: " . APP_URL . "/dashboard.php");
            exit;
        }

        $errors = [];
        $formData = $request;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!SecurityHelper::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                $errors[] = "Security token expired. Please reload and try again.";
            }

            $formData['patient_name']     = SecurityHelper::sanitizeString($_POST['patient_name'] ?? '');
            $formData['blood_group_id']   = (int)($_POST['blood_group_id'] ?? 0);
            $formData['units_required']   = max(1, min(10, (int)($_POST['units_required'] ?? 1)));
            $formData['urgency_level']    = $_POST['urgency_level'] ?? 'medium';
            $formData['hospital_name']    = SecurityHelper::sanitizeString($_POST['hospital_name'] ?? '');
            $formData['hospital_address'] = SecurityHelper::sanitizeString($_POST['hospital_address'] ?? '');
            $formData['city']             = SecurityHelper::sanitizeString($_POST['city'] ?? '');
            $formData['contact_number']   = SecurityHelper::sanitizeString($_POST['contact_number'] ?? '');
            $formData['required_date']    = $_POST['required_date'] ?? date('Y-m-d');
            $formData['medical_reason']   = SecurityHelper::sanitizeString($_POST['medical_reason'] ?? '');

            if (empty($formData['patient_name'])) {
                $errors[] = "Patient Name is required.";
            }
            if (empty($formData['hospital_name'])) {
                $errors[] = "Hospital Name is required.";
            }

            if (empty($errors)) {
                $pdo = Database::getInstance();
                $stmt = $pdo->prepare("
                    UPDATE blood_requests 
                    SET patient_name = :patient_name,
                        blood_group_id = :blood_group_id,
                        units_required = :units_required,
                        urgency_level = :urgency_level,
                        hospital_name = :hospital_name,
                        hospital_address = :hospital_address,
                        city = :city,
                        contact_number = :contact_number,
                        required_date = :required_date,
                        medical_reason = :medical_reason
                    WHERE id = :id AND requester_id = :requester_id
                ");

                $stmt->execute([
                    'patient_name'     => $formData['patient_name'],
                    'blood_group_id'   => $formData['blood_group_id'],
                    'units_required'   => $formData['units_required'],
                    'urgency_level'    => $formData['urgency_level'],
                    'hospital_name'    => $formData['hospital_name'],
                    'hospital_address' => $formData['hospital_address'],
                    'city'             => $formData['city'],
                    'contact_number'   => $formData['contact_number'],
                    'required_date'    => $formData['required_date'],
                    'medical_reason'   => $formData['medical_reason'],
                    'id'               => $requestId,
                    'requester_id'     => $userId
                ]);

                SessionHelper::setFlash('success', 'Blood request details updated successfully!');
                header("Location: " . APP_URL . "/request-detail.php?id=" . $requestId);
                exit;
            }
        }

        $pageTitle = "Edit Blood Request #" . $requestId;
        require_once ROOT_PATH . '/views/requests/edit.php';
    }
}
