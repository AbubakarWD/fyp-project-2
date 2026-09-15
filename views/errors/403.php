<?php
$pageTitle = "403 Forbidden Access";
require_once __DIR__ . '/../includes/header.php';
?>
<div class="container py-5 text-center my-5">
    <div class="bl-card bl-card-lg mx-auto" style="max-width: 550px;">
        <div class="bl-brand-icon bl-brand-icon-xl mx-auto mb-4">
            <i class="bi bi-shield-lock-fill"></i>
        </div>
        <h1 class="h2 text-dark mb-2">403 — Access Denied</h1>
        <p class="text-muted mb-4">You do not have permission to view or modify this page or resource.</p>
        <div class="d-flex justify-content-center gap-3">
            <a href="<?= APP_URL ?>/index.php" class="bl-btn bl-btn-light"><i class="bi bi-house"></i> Home</a>
            <a href="<?= APP_URL ?>/dashboard.php" class="bl-btn bl-btn-primary"><i class="bi bi-speedometer2"></i> Dashboard</a>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
