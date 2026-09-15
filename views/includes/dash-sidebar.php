<?php
/**
 * BloodLife — Dashboard Navigation Sidebar Component
 */
require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/helpers/SessionHelper.php';

$userRole = SessionHelper::getRole() ?? 'donor';
$currentPath = $_SERVER['PHP_SELF'] ?? '';
?>
<aside class="bl-sidebar" id="blSidebar">
    <div class="bl-sidebar-brand">
        <a href="<?= APP_URL ?>/dashboard.php" class="bl-navbar-brand">
            <span class="bl-brand-icon"><i class="bi bi-droplet-fill"></i></span>
            <span>Blood<span class="text-danger">Life</span></span>
        </a>
    </div>

    <div class="bl-sidebar-menu">
        <div class="bl-sidebar-heading">Core Navigation</div>
        <a href="<?= APP_URL ?>/dashboard.php" class="bl-sidebar-link <?= strpos($currentPath, 'dashboard.php') !== false ? 'active' : '' ?>">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>
        <a href="<?= APP_URL ?>/donors.php" class="bl-sidebar-link <?= strpos($currentPath, 'donors.php') !== false ? 'active' : '' ?>">
            <i class="bi bi-search"></i> Find Blood Donors
        </a>
        <a href="<?= APP_URL ?>/requests.php" class="bl-sidebar-link <?= strpos($currentPath, 'requests.php') !== false ? 'active' : '' ?>">
            <i class="bi bi-heart-pulse-fill"></i> Blood Requests
        </a>

        <?php if ($userRole === 'requester'): ?>
            <a href="<?= APP_URL ?>/request-create.php" class="bl-sidebar-link <?= strpos($currentPath, 'request-create.php') !== false ? 'active' : '' ?>">
                <i class="bi bi-plus-circle-fill"></i> Create Blood Request
            </a>
        <?php endif; ?>

        <div class="bl-sidebar-heading">Account & Activity</div>
        <a href="<?= APP_URL ?>/profile.php" class="bl-sidebar-link <?= strpos($currentPath, 'profile.php') !== false ? 'active' : '' ?>">
            <i class="bi bi-person-fill"></i> My Profile
        </a>
        <a href="<?= APP_URL ?>/donation_history.php" class="bl-sidebar-link <?= strpos($currentPath, 'donation_history.php') !== false ? 'active' : '' ?>">
            <i class="bi bi-journal-text"></i> Donation History
        </a>
        <a href="<?= APP_URL ?>/notifications.php" class="bl-sidebar-link <?= strpos($currentPath, 'notifications.php') !== false ? 'active' : '' ?>">
            <i class="bi bi-bell-fill"></i> Notifications
        </a>

        <div class="bl-sidebar-heading">Session</div>
        <a href="<?= APP_URL ?>/logout.php" class="bl-sidebar-link text-danger">
            <i class="bi bi-box-arrow-right text-danger"></i> Log Out
        </a>
    </div>
</aside>
