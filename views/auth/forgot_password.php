<?php
/**
 * BloodLife — Forgot Password View
 */
require_once ROOT_PATH . '/views/includes/header.php';
?>

<main class="py-5 my-md-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7 col-sm-10">
                <div class="bl-card bl-card-lg">
                    <div class="text-center mb-4">
                        <div class="bl-brand-icon bl-brand-icon-lg mx-auto mb-3">
                            <i class="bi bi-key-fill"></i>
                        </div>
                        <h1 class="h2 text-dark mb-1">Reset Password</h1>
                        <p class="text-muted small">Enter your registered email address to receive password recovery instructions.</p>
                    </div>

                    <?php if (!empty($error)): ?>
                        <div class="bl-alert bl-alert-danger mb-4">
                            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                            <div><?= e($error) ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($message)): ?>
                        <div class="bl-alert bl-alert-success mb-4">
                            <i class="bi bi-check-circle-fill fs-5"></i>
                            <div><?= e($message) ?></div>
                        </div>
                    <?php endif; ?>

                    <form action="<?= APP_URL ?>/forgot_password.php" method="POST" class="needs-validation" novalidate>
                        <?= SecurityHelper::csrfInput() ?>

                        <div class="mb-4">
                            <label class="bl-form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                                <input type="email" name="email" class="bl-form-control border-start-0" placeholder="name@example.com" required autofocus>
                            </div>
                        </div>

                        <button type="submit" class="bl-btn bl-btn-primary w-100 bl-btn-lg mb-3">
                            <i class="bi bi-send-fill"></i> Send Reset Link
                        </button>
                    </form>

                    <div class="text-center pt-3 border-top">
                        <a href="<?= APP_URL ?>/login.php" class="text-secondary small fw-bold">
                            <i class="bi bi-arrow-left"></i> Back to Login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once ROOT_PATH . '/views/includes/footer.php'; ?>
