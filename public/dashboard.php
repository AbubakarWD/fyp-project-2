<?php
/**
 * BloodLife — Authenticated Dashboard Router & Entry Point
 */

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/middleware/AuthMiddleware.php';
require_once __DIR__ . '/../app/models/User.php';
require_once __DIR__ . '/../app/controllers/DashboardController.php';

// Enforce Auth Guard
AuthMiddleware::requireAuth();

$userId = SessionHelper::getUserId();
$currentUser = User::findById($userId);

if (!$currentUser) {
    SessionHelper::logout();
    header("Location: " . APP_URL . "/login.php");
    exit;
}

$userRole = $currentUser['primary_role'];

if ($userRole === 'donor') {
    DashboardController::donorDashboard($currentUser);
} elseif ($userRole === 'requester') {
    DashboardController::recipientDashboard($currentUser);
} else {
    DashboardController::donorDashboard($currentUser);
}
