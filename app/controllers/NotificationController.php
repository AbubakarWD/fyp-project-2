<?php
/**
 * BloodLife — Notification Controller
 * Manages in-app notifications, mark-as-read status, and navbar bell feeds.
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/SecurityHelper.php';
require_once __DIR__ . '/../helpers/SessionHelper.php';
require_once __DIR__ . '/../helpers/FormatHelper.php';
require_once __DIR__ . '/../models/Notification.php';

class NotificationController {

    /**
     * Render full Notification Center Page.
     */
    public static function index(): void {
        SessionHelper::startSession();
        if (!SessionHelper::isLoggedIn()) {
            SessionHelper::setFlash('warning', 'Please log in to view your notifications.');
            header("Location: " . APP_URL . "/login.php");
            exit;
        }

        $userId = SessionHelper::getUserId();
        $notifications = Notification::getByUserId($userId, 50);
        $unreadCount = Notification::getUnreadCount($userId);

        $pageTitle = "Notification Center";
        require_once ROOT_PATH . '/views/notifications/index.php';
    }

    /**
     * Action to mark single notification as read.
     */
    public static function markRead(): void {
        SessionHelper::startSession();
        if (!SessionHelper::isLoggedIn()) {
            header("Location: " . APP_URL . "/login.php");
            exit;
        }

        $notificationId = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
        if ($notificationId > 0) {
            $userId = SessionHelper::getUserId();
            Notification::markAsRead($notificationId, $userId);
        }

        $redirect = $_GET['redirect'] ?? $_SERVER['HTTP_REFERER'] ?? (APP_URL . '/notifications.php');
        header("Location: " . $redirect);
        exit;
    }

    /**
     * Action to mark all notifications as read.
     */
    public static function markAllRead(): void {
        SessionHelper::startSession();
        if (!SessionHelper::isLoggedIn()) {
            header("Location: " . APP_URL . "/login.php");
            exit;
        }

        $userId = SessionHelper::getUserId();
        Notification::markAllAsRead($userId);
        SessionHelper::setFlash('success', 'All notifications marked as read.');

        header("Location: " . APP_URL . "/notifications.php");
        exit;
    }
}
