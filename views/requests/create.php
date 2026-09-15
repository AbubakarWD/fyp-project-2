<?php
/**
 * BloodLife — Create Blood Request View
 */
require_once ROOT_PATH . '/views/includes/header.php';
?>

<main class="py-5 my-md-3">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="bl-card bl-card-lg">
                    <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                        <div class="bl-brand-icon bl-brand-icon-lg">
                            <i class="bi bi-heart-pulse-fill"></i>
                        </div>
                        <div>
                            <h1 class="h3 text-dark mb-0">Create Emergency Blood Request</h1>
                            <p class="text-muted small mb-0">Fill out patient and hospital details to alert nearby voluntary blood donors instantly.</p>
                        </div>
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

                    <form action="<?= APP_URL ?>/request-create.php" method="POST" class="needs-validation" novalidate>
                        <?= SecurityHelper::csrfInput() ?>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="bl-form-label">Patient Full Name</label>
                                <input type="text" name="patient_name" class="bl-form-control" placeholder="e.g. Tariq Jenkins" value="<?= e($formData['patient_name'] ?? '') ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label class="bl-form-label">Required Blood Group</label>
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

                            <div class="col-md-6">
                                <label class="bl-form-label">Units Required (Bags)</label>
                                <input type="number" name="units_required" class="bl-form-control" min="1" max="10" value="<?= e($formData['units_required'] ?? 1) ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label class="bl-form-label">Urgency Level</label>
                                <select name="urgency_level" class="bl-form-select" required>
                                    <option value="critical" <?= ($formData['urgency_level'] ?? '') === 'critical' ? 'selected' : '' ?>>CRITICAL (Immediate Transfusion Needed)</option>
                                    <option value="high" <?= ($formData['urgency_level'] ?? '') === 'high' ? 'selected' : '' ?>>High (Needed within 12 Hours)</option>
                                    <option value="medium" <?= ($formData['urgency_level'] ?? '') === 'medium' ? 'selected' : '' ?>>Medium (Needed within 24 Hours)</option>
                                    <option value="low" <?= ($formData['urgency_level'] ?? '') === 'low' ? 'selected' : '' ?>>Low (Scheduled Surgery)</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="bl-form-label">Hospital Name</label>
                                <input type="text" name="hospital_name" class="bl-form-control" placeholder="e.g. Aga Khan University Hospital" value="<?= e($formData['hospital_name'] ?? '') ?>" required>
                            </div>

                            <div class="col-md-6">
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
                                <label class="bl-form-label">Contact Phone Number</label>
                                <input type="text" name="contact_number" class="bl-form-control" placeholder="+92 300 1234567" value="<?= e($formData['contact_number'] ?? '') ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label class="bl-form-label">Date Required By</label>
                                <input type="date" name="required_date" class="bl-form-control" value="<?= e($formData['required_date'] ?? '') ?>" required>
                            </div>

                            <div class="col-md-12">
                                <label class="bl-form-label">Hospital Address</label>
                                <input type="text" name="hospital_address" class="bl-form-control" placeholder="e.g. Stadium Road, Karachi" value="<?= e($formData['hospital_address'] ?? '') ?>">
                            </div>

                            <div class="col-md-12">
                                <label class="bl-form-label">Medical Reason / Additional Notes</label>
                                <textarea name="medical_reason" class="bl-form-control" rows="3" placeholder="e.g. Emergency surgery scheduled. Please contact patient attendant upon arrival."><?= e($formData['medical_reason'] ?? '') ?></textarea>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <a href="<?= APP_URL ?>/dashboard.php" class="bl-btn bl-btn-light">Cancel</a>
                            <button type="submit" class="bl-btn bl-btn-primary bl-btn-lg">
                                <i class="bi bi-send-fill"></i> Submit & Alert Donors
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once ROOT_PATH . '/views/includes/footer.php'; ?>
