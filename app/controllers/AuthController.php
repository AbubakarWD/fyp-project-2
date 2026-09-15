<?php
/**
 * BloodLife — Auth Controller
 * Handles user login, sign up, logout, and password recovery workflows.
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/SecurityHelper.php';
require_once __DIR__ . '/../helpers/SessionHelper.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {

    /**
     * Handle Login View & Post Action.
     */
    public static function login(): void {
        SessionHelper::startSession();
        if (SessionHelper::isLoggedIn()) {
            header("Location: " . APP_URL . "/dashboard.php");
            exit;
        }

        $errors = [];
        $email = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate CSRF
            if (!SecurityHelper::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                $errors[] = "Security token expired. Please reload and try again.";
            }

            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $remember = !empty($_POST['remember']);

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Please enter a valid email address.";
            }

            if (empty($password)) {
                $errors[] = "Please enter your password.";
            }

            if (empty($errors)) {
                $user = User::findByEmail($email);

                if ($user && SecurityHelper::verifyPassword($password, $user['password'])) {
                    if ($user['status'] !== 'active') {
                        $errors[] = "Your account is currently " . htmlspecialchars($user['status']) . ". Please contact support.";
                    } else {
                        // Complete Login
                        SessionHelper::login($user);
                        User::updateLastLogin($user['id']);

                        SessionHelper::setFlash('success', 'Welcome back, ' . htmlspecialchars($user['full_name'] ?? $user['username']) . '!');

                        header("Location: " . APP_URL . "/dashboard.php");
                        exit;
                    }
                } else {
                    $errors[] = "Invalid email or password credentials.";
                }
            }
        }

        // Render Login View
        $pageTitle = "Log In";
        require_once ROOT_PATH . '/views/auth/login.php';
    }

    /**
     * Handle Registration View & Post Action.
     */
    public static function signup(): void {
        SessionHelper::startSession();
        if (SessionHelper::isLoggedIn()) {
            header("Location: " . APP_URL . "/dashboard.php");
            exit;
        }

        $errors = [];
        $formData = [
            'full_name'      => '',
            'email'          => '',
            'phone_number'   => '',
            'blood_group_id' => '',
            'city'           => '',
            'primary_role'   => $_GET['role'] ?? 'donor'
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate CSRF
            if (!SecurityHelper::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                $errors[] = "Security token expired. Please reload and try again.";
            }

            $formData['full_name']      = SecurityHelper::sanitizeString($_POST['full_name'] ?? '');
            $formData['email']          = strtolower(trim($_POST['email'] ?? ''));
            $formData['phone_number']   = SecurityHelper::sanitizeString($_POST['phone_number'] ?? '');
            $formData['blood_group_id'] = (int)($_POST['blood_group_id'] ?? 0);
            $formData['city']           = SecurityHelper::sanitizeString($_POST['city'] ?? '');
            $formData['primary_role']   = $_POST['primary_role'] ?? 'donor';
            $password                   = $_POST['password'] ?? '';
            $confirmPassword            = $_POST['confirm_password'] ?? '';
            $terms                      = !empty($_POST['terms']);

            // Validation Checks
            if (empty($formData['full_name'])) {
                $errors[] = "Full Name is required.";
            }

            if (empty($formData['email']) || !filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "A valid email address is required.";
            } else {
                if (User::findByEmail($formData['email'])) {
                    $errors[] = "This email address is already registered. Please log in.";
                }
            }

            if (empty($formData['phone_number'])) {
                $errors[] = "Contact phone number is required.";
            }

            if (empty($formData['blood_group_id'])) {
                $errors[] = "Please select your blood group.";
            }

            if (empty($formData['city'])) {
                $errors[] = "Please select your city.";
            }

            if (!in_array($formData['primary_role'], ['donor', 'requester', 'both'])) {
                $formData['primary_role'] = 'donor';
            }

            if (strlen($password) < 8) {
                $errors[] = "Password must be at least 8 characters long.";
            }

            if ($password !== $confirmPassword) {
                $errors[] = "Passwords do not match.";
            }

            if (!$terms) {
                $errors[] = "You must agree to the Terms of Service & Privacy Policy.";
            }

            if (empty($errors)) {
                try {
                    // Generate unique username from email
                    $usernameBase = explode('@', $formData['email'])[0];
                    $username = preg_replace('/[^a-zA-Z0-9_]/', '', $usernameBase);
                    if (strlen($username) < 3) {
                        $username = 'user_' . rand(1000, 9999);
                    }
                    if (User::findByUsername($username)) {
                        $username .= '_' . rand(100, 999);
                    }

                    $registrationData = array_merge($formData, [
                        'username'      => $username,
                        'password_hash' => SecurityHelper::hashPassword($password)
                    ]);

                    $userId = User::register($registrationData);

                    // Fetch created user and log in immediately
                    $newUser = User::findById($userId);
                    SessionHelper::login($newUser);

                    SessionHelper::setFlash('success', 'Registration successful! Welcome to BloodLife.');
                    header("Location: " . APP_URL . "/dashboard.php");
                    exit;

                } catch (Exception $e) {
                    $errors[] = "An error occurred during registration. Please try again.";
                }
            }
        }

        // Render Sign Up View
        $pageTitle = "Sign Up";
        require_once ROOT_PATH . '/views/auth/signup.php';
    }

    /**
     * Handle User Logout.
     */
    public static function logout(): void {
        SessionHelper::logout();
        SessionHelper::setFlash('info', 'You have been logged out successfully.');
        header("Location: " . APP_URL . "/login.php");
        exit;
    }

    /**
     * Handle Forgot Password Flow.
     */
    public static function forgotPassword(): void {
        SessionHelper::startSession();
        $message = '';
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = strtolower(trim($_POST['email'] ?? ''));

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "Please enter a valid email address.";
            } else {
                $user = User::findByEmail($email);
                if ($user) {
                    // For security demo, simulate password reset email trigger
                    $message = "Password reset instructions have been sent to " . htmlspecialchars($email) . ". (For testing: use demo password 'Password123!')";
                } else {
                    // Avoid user enumeration
                    $message = "If an account with that email exists, reset instructions have been dispatched.";
                }
            }
        }

        $pageTitle = "Forgot Password";
        require_once ROOT_PATH . '/views/auth/forgot_password.php';
    }
}
