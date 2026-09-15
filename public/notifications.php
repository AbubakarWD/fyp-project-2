<?php
/**
 * BloodLife — Notification Route Entry Point
 */

require_once __DIR__ . '/../app/controllers/NotificationController.php';

$action = $_GET['action'] ?? 'index';

switch ($action) {
    case 'mark_read':
        NotificationController::markRead();
        break;
    case 'mark_all_read':
        NotificationController::markAllRead();
        break;
    default:
        NotificationController::index();
        break;
}
