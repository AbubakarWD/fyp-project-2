<?php
/**
 * BloodLife — AJAX API: Get Unread Notifications & Bell Counter
 */

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/helpers/SessionHelper.php';
require_once __DIR__ . '/../../app/helpers/SecurityHelper.php';
require_once __DIR__ . '/../../app/helpers/FormatHelper.php';
require_once __DIR__ . '/../../app/models/Notification.php';

SessionHelper::startSession();

if (!SessionHelper::isLoggedIn()) {
    echo json_encode(['success' => false, 'unread_count' => 0, 'notifications' => []]);
    exit;
}

$userId = SessionHelper::getUserId();
$unreadCount = Notification::getUnreadCount($userId);
$recentNotifications = Notification::getByUserId($userId, 5);

$formattedNotifications = array_map(function($item) {
    return [
        'id'          => (int)$item['id'],
        'type'        => SecurityHelper::sanitizeOutput($item['type']),
        'title'       => SecurityHelper::sanitizeOutput($item['title']),
        'message'     => SecurityHelper::sanitizeOutput($item['message']),
        'action_link' => SecurityHelper::sanitizeOutput($item['action_link'] ?? '#'),
        'is_read'     => (int)$item['is_read'],
        'created_at'  => FormatHelper::timeAgo($item['created_at'])
    ];
}, $recentNotifications);

echo json_encode([
    'success'       => true,
    'unread_count'  => $unreadCount,
    'notifications' => $formattedNotifications
]);
exit;
