<?php
/**
 * BloodLife — Phase 11 In-App Notification Engine Test Suite
 */

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/models/Notification.php';
require_once __DIR__ . '/../app/models/UserProfile.php';

echo "========================================================\n";
echo "BloodLife — Phase 11 Notification Engine Test Suite\n";
echo "========================================================\n";

$passed = 0;
$failed = 0;

function assertTest(bool $condition, string $description): void {
    global $passed, $failed;
    if ($condition) {
        echo "[PASS] {$description}\n";
        $passed++;
    } else {
        echo "[FAIL] {$description}\n";
        $failed++;
    }
}

try {
    // 1. Fetch test user profile
    $user = UserProfile::findByEmail('donor1@bloodlife.org');
    assertTest(!empty($user), "Target user profile retrieved");

    $userId = $user['user_id'];
    $initialUnread = Notification::getUnreadCount($userId);

    // 2. Dispatch a new notification
    $notifId = Notification::create(
        $userId,
        'donor_match',
        'Test Match Alert',
        'You have a new matching blood request in Karachi.',
        'requests.php'
    );
    assertTest($notifId > 0, "Notification::create created alert ID {$notifId}");

    // 3. Verify unread count incremented
    $newUnread = Notification::getUnreadCount($userId);
    assertTest($newUnread === $initialUnread + 1, "Unread notification count incremented by 1");

    // 4. Retrieve notifications list
    $list = Notification::getByUserId($userId, 10);
    assertTest(!empty($list) && count($list) > 0, "Notification::getByUserId returns array");
    assertTest($list[0]['title'] === 'Test Match Alert', "Top notification title matches created alert");

    // 5. Mark single notification as read
    $marked = Notification::markAsRead($notifId, $userId);
    assertTest($marked, "Notification::markAsRead executed");

    $afterMarkUnread = Notification::getUnreadCount($userId);
    assertTest($afterMarkUnread === $initialUnread, "Unread count decreased after marking single item read");

    // 6. Test markAllAsRead
    // Dispatch 2 more notifications
    Notification::create($userId, 'emergency', 'Urgent Alert 1', 'Sample urgent text 1');
    Notification::create($userId, 'request_update', 'Urgent Alert 2', 'Sample urgent text 2');
    assertTest(Notification::getUnreadCount($userId) >= 2, "Dispatched 2 more unread notifications");

    $allMarked = Notification::markAllAsRead($userId);
    assertTest($allMarked, "Notification::markAllAsRead executed");
    assertTest(Notification::getUnreadCount($userId) === 0, "Unread notification count is now 0");

} catch (Exception $e) {
    echo "[FAIL] Unexpected Exception: " . $e->getMessage() . "\n";
    $failed++;
}

echo "========================================================\n";
echo "SUMMARY: Passed: {$passed} | Failed: {$failed}\n";
echo "========================================================\n";
exit($failed === 0 ? 0 : 1);
