<?php
/**
 * BloodLife — Login View
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
                            <i class="bi bi-droplet-fill"></i>
                        </div>
                        <h1 class="h2 text-dark mb-1">Welcome Back</h1>
                        <p class="text-muted small">Sign in to manage your blood requests or donor profile.</p>
                    </div>

                    <?php if (!empty($errors)): ?>
                        <div class="bl-alert bl-alert-danger mb-4">
                            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                            <div>
                                <?php foreach ($errors as $err): ?>
                                    <div><?= e($err) ?></div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <form action="<?= APP_URL ?>/login.php" method="POST" class="needs-validation" novalidate>
                        <?= SecurityHelper::csrfInput() ?>

                        <div class="mb-3">
                            <label class="bl-form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                                <input type="email" name="email" class="bl-form-control border-start-0" placeholder="name@example.com" value="<?= e($email ?? '') ?>" required autofocus>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="bl-form-label mb-0">Password</label>
                                <a href="<?= APP_URL ?>/forgot_password.php" class="small text-danger font-semibold">Forgot Password?</a>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                                <input type="password" name="password" class="bl-form-control border-start-0" placeholder="••••••••" required>
                            </div>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="remember" id="rememberMe">
                            <label class="form-check-label small text-muted" for="rememberMe">
                                Remember me on this device
                            </label>
                        </div>

                        <button type="submit" class="bl-btn bl-btn-primary w-100 bl-btn-lg mb-3">
                            <i class="bi bi-box-arrow-in-right"></i> Log In
                        </button>
                    </form>

                    <!-- Demo Accounts Quick Fill -->
                    <div class="p-3 bg-light rounded border text-center mb-4">
                        <small class="fw-bold text-dark d-block mb-2"><i class="bi bi-info-circle text-primary me-1"></i> Testing Demo Accounts (Pass: Password123!)</small>
                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                            <button type="button" class="bl-btn bl-btn-light bl-btn-sm" onclick="document.querySelector('[name=email]').value='donor1@bloodlife.org'; document.querySelector('[name=password]').value='Password123!';">
                                <i class="bi bi-person-fill text-danger me-1"></i> Donor Demo
                            </button>
                            <button type="button" class="bl-btn bl-btn-light bl-btn-sm" onclick="document.querySelector('[name=email]').value='requester1@bloodlife.org'; document.querySelector('[name=password]').value='Password123!';">
                                <i class="bi bi-heart-pulse-fill text-primary me-1"></i> Requester Demo
                            </button>
                        </div>
                    </div>

                    <div class="text-center pt-3 border-top">
                        <p class="text-muted small mb-0">
                            Don't have an account yet? 
                            <a href="<?= APP_URL ?>/signup.php" class="text-danger fw-bold ms-1">Create an Account</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once ROOT_PATH . '/views/includes/footer.php'; ?>
