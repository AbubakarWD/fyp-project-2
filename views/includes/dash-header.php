<?php
/**
 * BloodLife — Dashboard Top Navigation Bar Component
 */
require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/helpers/SecurityHelper.php';
require_once __DIR__ . '/../../app/helpers/SessionHelper.php';

$userId = SessionHelper::getUserId();
$fullName = $_SESSION['full_name'] ?? 'User';
$userRole = SessionHelper::getRole();
$bloodGroup = $_SESSION['blood_group'] ?? 'O+';
$avatar = $_SESSION['avatar'] ?? 'default_avatar.png';
?>
<?php
require_once __DIR__ . '/../../app/models/Notification.php';
$unreadCount = Notification::getUnreadCount($userId);
$unreadItems = Notification::getByUserId($userId, 5);
?>
<header class="bl-dash-topbar">
    <div class="d-flex align-items-center gap-3">
        <button type="button" id="blSidebarToggle" class="bl-btn bl-btn-light bl-btn-icon d-lg-none" aria-label="Toggle Sidebar">
            <i class="bi bi-list fs-4"></i>
        </button>
        <div class="d-none d-sm-flex align-items-center gap-2">
            <span class="fs-5 fw-bold text-dark">Welcome back, <?= e($fullName) ?></span>
            <span class="bl-badge bl-badge-blood"><?= e($bloodGroup) ?></span>
            <span class="bl-badge <?= $userRole === 'donor' ? 'bl-badge-success' : 'bl-badge-primary' ?>">
                <?= ucfirst(e($userRole)) ?>
            </span>
        </div>
    </div>

    <div class="d-flex align-items-center gap-3">
        <!-- Quick Public Nav Link -->
        <a href="<?= APP_URL ?>/index.php" class="bl-btn bl-btn-light bl-btn-sm d-none d-md-inline-flex" target="_blank">
            <i class="bi bi-globe"></i> View Site
        </a>

        <!-- Notifications Dropdown -->
        <div class="dropdown">
            <button class="bl-btn bl-btn-light bl-btn-icon position-relative" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notifications">
                <i class="bi bi-bell fs-5"></i>
                <?php if ($unreadCount > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light">
                        <?= $unreadCount > 99 ? '99+' : $unreadCount ?>
                    </span>
                <?php endif; ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-2" style="width: 320px;">
                <li class="dropdown-header fw-bold text-dark border-bottom pb-2 d-flex align-items-center justify-content-between">
                    <span>Notifications (<?= $unreadCount ?>)</span>
                    <a href="<?= APP_URL ?>/notifications.php" class="small text-danger text-decoration-none">View All</a>
                </li>
                <?php if (empty($unreadItems)): ?>
                    <li class="p-3 text-center text-muted small">No notifications right now.</li>
                <?php else: ?>
                    <?php foreach ($unreadItems as $notif): ?>
                        <li>
                            <a class="dropdown-item p-2 rounded small text-wrap mt-1 <?= $notif['is_read'] ? '' : 'fw-semibold bg-light' ?>" 
                               href="<?= APP_URL ?>/notifications.php?action=mark_read&id=<?= $notif['id'] ?>&redirect=<?= urlencode(APP_URL . '/' . ltrim($notif['action_link'] ?? 'notifications.php', '/')) ?>">
                                <div class="text-dark <?= $notif['is_read'] ? '' : 'fw-bold' ?>"><?= e($notif['title']) ?></div>
                                <div class="text-muted fs-caption text-truncate"><?= e($notif['message']) ?></div>
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>

        <!-- User Profile Menu -->
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="<?= APP_URL ?>/assets/images/default_avatar.png" alt="<?= e($fullName) ?>" class="bl-avatar">
                <span class="d-none d-md-inline font-semibold text-dark"><?= e($fullName) ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                <li><a class="dropdown-item" href="<?= APP_URL ?>/profile.php"><i class="bi bi-person me-2"></i> My Profile</a></li>
                <li><a class="dropdown-item" href="<?= APP_URL ?>/settings.php"><i class="bi bi-gear me-2"></i> Settings</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="<?= APP_URL ?>/logout.php"><i class="bi bi-box-arrow-right me-2"></i> Log Out</a></li>
            </ul>
        </div>
    </div>
</header>
