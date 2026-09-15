<?php
$pageTitle = "500 Internal Server Error";
require_once __DIR__ . '/../includes/header.php';
?>
<div class="container py-5 text-center my-5">
    <div class="bl-card bl-card-lg mx-auto" style="max-width: 550px;">
        <div class="bl-brand-icon bl-brand-icon-xl bl-brand-icon-amber mx-auto mb-4">
            <i class="bi bi-exclamation-octagon-fill"></i>
        </div>
        <h1 class="h2 text-dark mb-2">500 — Server Error</h1>
        <p class="text-muted mb-4">An unexpected server error occurred while processing your request. Please try again shortly.</p>
        <div class="d-flex justify-content-center gap-3">
            <a href="<?= APP_URL ?>/index.php" class="bl-btn bl-btn-primary"><i class="bi bi-house"></i> Return Home</a>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
