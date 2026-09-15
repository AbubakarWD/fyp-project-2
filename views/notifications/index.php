<?php
require_once ROOT_PATH . '/views/includes/header.php';
require_once ROOT_PATH . '/views/includes/dash-header.php';
?>

<div class="container-fluid py-4 dashboard-wrapper">
    <div class="row g-4">
        <!-- Sidebar Navigation -->
        <div class="col-lg-3">
            <?php require_once ROOT_PATH . '/views/includes/dash-sidebar.php'; ?>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            <!-- Header Banner -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 pb-2 border-bottom">
                <div>
                    <h2 class="h3 fw-bold mb-1 text-slate-800">
                        Notification Center
                        <?php if ($unreadCount > 0): ?>
                            <span class="badge bg-danger rounded-pill fs-6 ms-2"><?= $unreadCount ?> Unread</span>
                        <?php endif; ?>
                    </h2>
                    <p class="text-muted small mb-0">Stay updated on match alerts, donor responses, and emergency request updates.</p>
                </div>
                <?php if ($unreadCount > 0): ?>
                    <a href="<?= APP_URL ?>/notifications.php?action=mark_all_read" class="btn btn-outline-danger btn-sm rounded-pill px-3 shadow-sm">
                        <i class="bi bi-check2-all me-1"></i> Mark All as Read
                    </a>
                <?php endif; ?>
            </div>

            <!-- Notifications List Card -->
            <div class="bl-card mb-0">
                <div class="bl-card-header">
                    <h5 class="fw-bold mb-0 text-slate-800"><i class="bi bi-bell-fill text-danger me-2"></i> Activity Feed</h5>
                    <span class="text-muted small">Showing last 50 alerts</span>
                </div>

                <div class="card-body p-4">
                    <?php if (empty($notifications)): ?>
                        <div class="text-center py-5">
                            <div class="mb-3 text-muted display-4"><i class="bi bi-bell-slash"></i></div>
                            <h5 class="fw-semibold text-slate-700">No Notifications Yet</h5>
                            <p class="text-muted small max-w-md mx-auto">You're all caught up! When there are emergency matches or responses to your requests, alerts will appear here.</p>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush gap-2">
                            <?php foreach ($notifications as $item): ?>
                                <?php
                                    $isRead = (bool)$item['is_read'];
                                    $type = $item['type'];
                                    
                                    // Set icon and badge based on type
                                    $iconClass = 'bi-bell-fill text-primary';
                                    $bgClass = 'bg-primary bg-opacity-10';
                                    if ($type === 'donor_match' || $type === 'emergency') {
                                        $iconClass = 'bi-heart-pulse-fill text-danger';
                                        $bgClass = 'bg-danger bg-opacity-10';
                                    } elseif ($type === 'new_response' || $type === 'accepted') {
                                        $iconClass = 'bi-check-circle-fill text-success';
                                        $bgClass = 'bg-success bg-opacity-10';
                                    } elseif ($type === 'cancelled' || $type === 'rejected') {
                                        $iconClass = 'bi-x-circle-fill text-warning';
                                        $bgClass = 'bg-warning bg-opacity-10';
                                    }
                                ?>
                                <div class="list-group-item border rounded-3 p-3 transition-all <?= $isRead ? 'bg-white' : 'bg-light border-danger border-opacity-25 shadow-sm' ?>">
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="rounded-circle <?= $bgClass ?> p-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px;">
                                            <i class="bi <?= $iconClass ?> fs-4"></i>
                                        </div>

                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <h6 class="fw-bold mb-1 <?= $isRead ? 'text-slate-800' : 'text-crimson-600' ?>">
                                                    <?= SecurityHelper::sanitizeOutput($item['title']) ?>
                                                    <?php if (!$isRead): ?>
                                                        <span class="badge bg-danger rounded-pill ms-2 fs-7">New</span>
                                                    <?php endif; ?>
                                                </h6>
                                                <small class="text-muted ms-2 flex-shrink-0">
                                                    <i class="bi bi-clock me-1"></i><?= FormatHelper::timeAgo($item['created_at']) ?>
                                                </small>
                                            </div>

                                            <p class="text-slate-600 mb-2 small">
                                                <?= SecurityHelper::sanitizeOutput($item['message']) ?>
                                            </p>

                                            <div class="d-flex gap-2 align-items-center">
                                                <?php if (!empty($item['action_link'])): ?>
                                                    <a href="<?= APP_URL ?>/notifications.php?action=mark_read&id=<?= $item['id'] ?>&redirect=<?= urlencode(APP_URL . '/' . ltrim($item['action_link'], '/')) ?>" 
                                                       class="btn btn-sm btn-danger rounded-pill px-3 py-1 fs-7 fw-semibold">
                                                        View Details <i class="bi bi-arrow-right ms-1"></i>
                                                    </a>
                                                <?php endif; ?>

                                                <?php if (!$isRead): ?>
                                                    <a href="<?= APP_URL ?>/notifications.php?action=mark_read&id=<?= $item['id'] ?>" 
                                                       class="btn btn-sm btn-light border text-muted rounded-pill px-3 py-1 fs-7">
                                                        Mark as Read
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . '/views/includes/footer.php'; ?>
