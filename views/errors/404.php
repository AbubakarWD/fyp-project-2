<?php
$pageTitle = "404 Page Not Found";
require_once __DIR__ . '/../includes/header.php';
?>
<div class="container py-5 text-center my-5">
    <div class="bl-card bl-card-lg mx-auto" style="max-width: 550px;">
        <div class="bl-brand-icon bl-brand-icon-xl bl-brand-icon-blue mx-auto mb-4">
            <i class="bi bi-question-circle-fill"></i>
        </div>
        <h1 class="h2 text-dark mb-2">404 — Page Not Found</h1>
        <p class="text-muted mb-4">The page or resource you requested could not be found or has moved.</p>
        <div class="d-flex justify-content-center gap-3">
            <a href="<?= APP_URL ?>/index.php" class="bl-btn bl-btn-light"><i class="bi bi-house"></i> Home</a>
            <a href="<?= APP_URL ?>/donors.php" class="bl-btn bl-btn-primary"><i class="bi bi-search"></i> Find Donors</a>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
