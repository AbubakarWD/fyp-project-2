<?php
/**
 * BloodLife — Sign Up / Registration View
 */
require_once ROOT_PATH . '/views/includes/header.php';
?>

<main class="py-5 my-md-3">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-9 col-sm-11">
                <div class="bl-card bl-card-lg">
                    <div class="text-center mb-4">
                        <div class="bl-brand-icon bl-brand-icon-lg mx-auto mb-3">
                            <i class="bi bi-droplet-fill"></i>
                        </div>
                        <h1 class="h2 text-dark mb-1">Create BloodLife Account</h1>
                        <p class="text-muted small">Join our social blood donation community as a voluntary donor or requester.</p>
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

                    <form action="<?= APP_URL ?>/signup.php" method="POST" class="needs-validation" novalidate>
                        <?= SecurityHelper::csrfInput() ?>

                        <!-- Role Selector -->
                        <div class="mb-4">
                            <label class="bl-form-label mb-2">I want to register as:</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="primary_role" id="roleDonor" value="donor" <?= ($formData['primary_role'] ?? 'donor') === 'donor' ? 'checked' : '' ?>>
                                    <label class="btn btn-outline-danger w-100 p-3 rounded-3 text-start" for="roleDonor">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-heart-fill fs-4 text-danger"></i>
                                            <div>
                                                <div class="fw-bold">Blood Donor</div>
                                                <div class="small opacity-75">I want to donate blood</div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="primary_role" id="roleRequester" value="requester" <?= ($formData['primary_role'] ?? '') === 'requester' ? 'checked' : '' ?>>
                                    <label class="btn btn-outline-primary w-100 p-3 rounded-3 text-start" for="roleRequester">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-heart-pulse-fill fs-4 text-primary"></i>
                                            <div>
                                                <div class="fw-bold">Requester</div>
                                                <div class="small opacity-75">I need blood for a patient</div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="bl-form-label">Full Name</label>
                                <input type="text" name="full_name" class="bl-form-control" placeholder="e.g. John Doe" value="<?= e($formData['full_name'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="bl-form-label">Email Address</label>
                                <input type="email" name="email" class="bl-form-control" placeholder="name@example.com" value="<?= e($formData['email'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="bl-form-label">Phone Number</label>
                                <input type="text" name="phone_number" class="bl-form-control" placeholder="+92 300 1234567" value="<?= e($formData['phone_number'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="bl-form-label">Blood Group</label>
                                <select name="blood_group_id" class="bl-form-select" required>
                                    <option value="">Select Blood Group</option>
                                    <option value="1" <?= ($formData['blood_group_id'] ?? '') == 1 ? 'selected' : '' ?>>A Positive (A+)</option>
                                    <option value="2" <?= ($formData['blood_group_id'] ?? '') == 2 ? 'selected' : '' ?>>A Negative (A-)</option>
                                    <option value="3" <?= ($formData['blood_group_id'] ?? '') == 3 ? 'selected' : '' ?>>B Positive (B+)</option>
                                    <option value="4" <?= ($formData['blood_group_id'] ?? '') == 4 ? 'selected' : '' ?>>B Negative (B-)</option>
                                    <option value="5" <?= ($formData['blood_group_id'] ?? '') == 5 ? 'selected' : '' ?>>AB Positive (AB+)</option>
                                    <option value="6" <?= ($formData['blood_group_id'] ?? '') == 6 ? 'selected' : '' ?>>AB Negative (AB-)</option>
                                    <option value="7" <?= ($formData['blood_group_id'] ?? '') == 7 ? 'selected' : '' ?>>O Positive (O+)</option>
                                    <option value="8" <?= ($formData['blood_group_id'] ?? '') == 8 ? 'selected' : '' ?>>O Negative (O-)</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="bl-form-label">City / Location</label>
                                <select name="city" class="bl-form-select" required>
                                    <option value="">Select City</option>
                                    <option value="Karachi" <?= ($formData['city'] ?? '') === 'Karachi' ? 'selected' : '' ?>>Karachi</option>
                                    <option value="Lahore" <?= ($formData['city'] ?? '') === 'Lahore' ? 'selected' : '' ?>>Lahore</option>
                                    <option value="Islamabad" <?= ($formData['city'] ?? '') === 'Islamabad' ? 'selected' : '' ?>>Islamabad</option>
                                    <option value="Rawalpindi" <?= ($formData['city'] ?? '') === 'Rawalpindi' ? 'selected' : '' ?>>Rawalpindi</option>
                                    <option value="Faisalabad" <?= ($formData['city'] ?? '') === 'Faisalabad' ? 'selected' : '' ?>>Faisalabad</option>
                                    <option value="Multan" <?= ($formData['city'] ?? '') === 'Multan' ? 'selected' : '' ?>>Multan</option>
                                    <option value="Peshawar" <?= ($formData['city'] ?? '') === 'Peshawar' ? 'selected' : '' ?>>Peshawar</option>
                                    <option value="Quetta" <?= ($formData['city'] ?? '') === 'Quetta' ? 'selected' : '' ?>>Quetta</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="bl-form-label">Password</label>
                                <input type="password" name="password" class="bl-form-control" placeholder="At least 8 characters" minlength="8" required>
                            </div>
                            <div class="col-md-6">
                                <label class="bl-form-label">Confirm Password</label>
                                <input type="password" name="confirm_password" class="bl-form-control" placeholder="Re-enter password" required>
                            </div>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="terms" id="agreeTerms" required>
                            <label class="form-check-label small text-muted" for="agreeTerms">
                                I agree to the BloodLife <a href="#" class="text-danger">Terms of Service</a> and <a href="#" class="text-danger">Privacy Policy</a>.
                            </label>
                        </div>

                        <button type="submit" class="bl-btn bl-btn-primary w-100 bl-btn-lg mb-3">
                            <i class="bi bi-person-plus-fill"></i> Create Account
                        </button>
                    </form>

                    <div class="text-center pt-3 border-top">
                        <p class="text-muted small mb-0">
                            Already have an account? 
                            <a href="<?= APP_URL ?>/login.php" class="text-danger fw-bold ms-1">Log In Instead</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once ROOT_PATH . '/views/includes/footer.php'; ?>
