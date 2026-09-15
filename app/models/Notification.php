<?php
/**
 * BloodLife — Notification Model
 * In-app system alerts and notifications data access.
 */

require_once __DIR__ . '/../config/database.php';

class Notification {

    /**
     * Create a new notification for a specific user.
     *
     * @param int $userId
     * @param string $type E.g. 'donor_match', 'new_response', 'request_update'
     * @param string $title
     * @param string $message
     * @param string|null $actionLink
     * @return int Created Notification ID
     */
    public static function create(int $userId, string $type, string $title, string $message, ?string $actionLink = null): int {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("
            INSERT INTO notifications (user_id, type, title, message, action_link, is_read)
            VALUES (:user_id, :type, :title, :message, :action_link, 0)
        ");

        $stmt->execute([
            'user_id'     => $userId,
            'type'        => trim($type),
            'title'       => trim($title),
            'message'     => trim($message),
            'action_link' => $actionLink
        ]);

        return (int)$pdo->lastInsertId();
    }

    /**
     * Get notifications for a user ordered by created_at DESC.
     *
     * @param int $userId
     * @param int $limit
     * @return array
     */
    public static function getByUserId(int $userId, int $limit = 20): array {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("
            SELECT * FROM notifications
            WHERE user_id = :user_id
            ORDER BY created_at DESC, id DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get count of unread notifications for a user.
     *
     * @param int $userId
     * @return int
     */
    public static function getUnreadCount(int $userId): int {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = :user_id AND is_read = 0");
        $stmt->execute(['user_id' => $userId]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Mark single notification as read.
     *
     * @param int $id
     * @param int $userId
     * @return bool
     */
    public static function markAsRead(int $id, int $userId): bool {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = :id AND user_id = :user_id");
        return $stmt->execute(['id' => $id, 'user_id' => $userId]);
    }

    /**
     * Mark all notifications as read for a user.
     *
     * @param int $userId
     * @return bool
     */
    public static function markAllAsRead(int $userId): bool {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = :user_id");
        return $stmt->execute(['user_id' => $userId]);
    }
}
