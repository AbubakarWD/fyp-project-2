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

        <!-- Main Profile Body -->
        <div class="col-lg-9">
            <!-- Header Banner -->
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <div>
                    <h2 class="h3 fw-bold mb-1 text-slate-800">My Profile & Settings</h2>
                    <p class="text-muted small mb-0">Manage your personal profile, donation preferences, and contact information.</p>
                </div>
                <a href="<?= APP_URL ?>/dashboard.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
                </a>
            </div>

            <div class="row g-4">
                <!-- Left Column: Avatar & Account Summary -->
                <div class="col-md-4">
                    <div class="bl-card bl-card-flush mb-4">
                        <div class="bg-crimson-gradient py-4 text-center text-white position-relative">
                            <div class="avatar-wrapper mb-3 position-relative d-inline-block">
                                <?php if (!empty($profile['avatar']) && file_exists(PUBLIC_PATH . '/uploads/avatars/' . $profile['avatar'])): ?>
                                    <img src="<?= APP_URL ?>/uploads/avatars/<?= SecurityHelper::sanitizeOutput($profile['avatar']) ?>" 
                                         alt="Profile Avatar" 
                                         class="rounded-circle border border-4 border-white shadow" 
                                         style="width: 120px; height: 120px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="rounded-circle border border-4 border-white shadow bg-white text-danger fw-bold d-flex align-items-center justify-content-center mx-auto" 
                                         style="width: 120px; height: 120px; font-size: 2.5rem;">
                                        <?= strtoupper(substr($profile['full_name'] ?? 'U', 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                                <span class="badge bg-danger rounded-pill position-absolute bottom-0 end-0 shadow px-2 py-1 fs-7">
                                    <?= SecurityHelper::sanitizeOutput($profile['blood_group'] ?? 'N/A') ?>
                                </span>
                            </div>

                            <h5 class="fw-bold mb-0 text-white"><?= SecurityHelper::sanitizeOutput($profile['full_name']) ?></h5>
                            <p class="text-white-50 small mb-0"><?= SecurityHelper::sanitizeOutput($profile['email']) ?></p>
                            <span class="badge bg-white text-dark mt-2 text-capitalize fw-semibold">
                                Role: <?= SecurityHelper::sanitizeOutput($profile['primary_role']) ?>
                            </span>
                        </div>

                        <div class="card-body p-4">
                            <!-- Avatar Upload Form -->
                            <form action="<?= APP_URL ?>/profile.php?action=avatar" method="POST" enctype="multipart/form-data" class="mb-4">
                                <?= SecurityHelper::getCsrfTokenInput() ?>
                                <label class="form-label fw-semibold small text-muted">Update Profile Picture</label>
                                <div class="input-group input-group-sm mb-2">
                                    <input type="file" name="avatar" class="form-control rounded-start" accept="image/jpeg,image/png,image/webp" required>
                                    <button type="submit" class="btn btn-danger px-3">
                                        <i class="bi bi-upload me-1"></i> Upload
                                    </button>
                                </div>
                                <div class="form-text fs-7">Max 3MB. JPG, PNG, WEBP only.</div>
                            </form>

                            <hr class="text-muted opacity-25">

                            <!-- Statistics Summary -->
                            <div class="space-y-3">
                                <div class="d-flex justify-content-between align-items-center py-1">
                                    <span class="text-muted small"><i class="bi bi-heart-pulse me-2 text-danger"></i> Total Donations:</span>
                                    <span class="fw-bold text-slate-800"><?= (int)($profile['total_donations_count'] ?? 0) ?></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center py-1">
                                    <span class="text-muted small"><i class="bi bi-calendar-check me-2 text-primary"></i> Last Donated:</span>
                                    <span class="fw-semibold text-slate-700">
                                        <?= !empty($profile['last_donation_date']) ? FormatHelper::formatDate($profile['last_donation_date']) : 'Never' ?>
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center py-1">
                                    <span class="text-muted small"><i class="bi bi-geo-alt me-2 text-success"></i> Location:</span>
                                    <span class="fw-semibold text-slate-700"><?= SecurityHelper::sanitizeOutput($profile['city']) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Profile Edit Form -->
                <div class="col-md-8">
                    <div class="bl-card mb-0">
                        <div class="bl-card-header">
                            <h5 class="fw-bold mb-0 text-slate-800"><i class="bi bi-person-lines-fill text-danger me-2"></i> Edit Personal Details</h5>
                        </div>
                        <div class="card-body p-4">
                            <form action="<?= APP_URL ?>/profile.php?action=update" method="POST">
                                <?= SecurityHelper::getCsrfTokenInput() ?>

                                <div class="row g-3 mb-3">
                                    <!-- Full Name -->
                                    <div class="col-md-6">
                                        <label for="full_name" class="form-label fw-semibold text-slate-700">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               id="full_name" 
                                               name="full_name" 
                                               class="form-control form-control-lg rounded-3 fs-6" 
                                               value="<?= SecurityHelper::sanitizeOutput($profile['full_name']) ?>" 
                                               required>
                                    </div>

                                    <!-- Phone Number -->
                                    <div class="col-md-6">
                                        <label for="phone_number" class="form-label fw-semibold text-slate-700">Phone Number <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               id="phone_number" 
                                               name="phone_number" 
                                               class="form-control form-control-lg rounded-3 fs-6" 
                                               value="<?= SecurityHelper::sanitizeOutput($profile['phone_number']) ?>" 
                                               required>
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <!-- Blood Group -->
                                    <div class="col-md-6">
                                        <label for="blood_group_id" class="form-label fw-semibold text-slate-700">Blood Group <span class="text-danger">*</span></label>
                                        <select id="blood_group_id" name="blood_group_id" class="form-select form-select-lg rounded-3 fs-6" required>
                                            <option value="">Select Blood Group</option>
                                            <?php foreach ($bloodGroups as $bg): ?>
                                                <option value="<?= $bg['id'] ?>" <?= ($profile['blood_group_id'] == $bg['id']) ? 'selected' : '' ?>>
                                                    <?= SecurityHelper::sanitizeOutput($bg['code']) ?> (<?= SecurityHelper::sanitizeOutput($bg['display_name']) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <!-- Gender -->
                                    <div class="col-md-6">
                                        <label for="gender" class="form-label fw-semibold text-slate-700">Gender</label>
                                        <select id="gender" name="gender" class="form-select form-select-lg rounded-3 fs-6">
                                            <option value="male" <?= ($profile['gender'] === 'male') ? 'selected' : '' ?>>Male</option>
                                            <option value="female" <?= ($profile['gender'] === 'female') ? 'selected' : '' ?>>Female</option>
                                            <option value="other" <?= ($profile['gender'] === 'other') ? 'selected' : '' ?>>Other</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <!-- City -->
                                    <div class="col-md-6">
                                        <label for="city" class="form-label fw-semibold text-slate-700">City <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               id="city" 
                                               name="city" 
                                               class="form-control form-control-lg rounded-3 fs-6" 
                                               value="<?= SecurityHelper::sanitizeOutput($profile['city']) ?>" 
                                               required>
                                    </div>

                                    <!-- Address -->
                                    <div class="col-md-6">
                                        <label for="address" class="form-label fw-semibold text-slate-700">Address / Neighborhood</label>
                                        <input type="text" 
                                               id="address" 
                                               name="address" 
                                               class="form-control form-control-lg rounded-3 fs-6" 
                                               value="<?= SecurityHelper::sanitizeOutput($profile['address'] ?? '') ?>">
                                    </div>
                                </div>

                                <!-- Bio -->
                                <div class="mb-4">
                                    <label for="bio" class="form-label fw-semibold text-slate-700">Short Bio / Donor Statement</label>
                                    <textarea id="bio" 
                                              name="bio" 
                                              rows="3" 
                                              class="form-control rounded-3" 
                                              placeholder="Share details about your availability, blood donation history, or readiness to help..."><?= SecurityHelper::sanitizeOutput($profile['bio'] ?? '') ?></textarea>
                                </div>

                                <?php if ($profile['primary_role'] === 'donor' || $profile['primary_role'] === 'both'): ?>
                                    <!-- Donor Availability Switch -->
                                    <div class="p-3 bg-light rounded-3 mb-4 d-flex align-items-center justify-content-between border">
                                        <div>
                                            <h6 class="fw-bold mb-1 text-slate-800"><i class="bi bi-toggle-on text-danger me-2"></i> Donor Availability Status</h6>
                                            <p class="text-muted small mb-0">When turned ON, recipients can discover you in blood search results.</p>
                                        </div>
                                        <div class="form-check form-switch form-switch-md mb-0">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   name="is_available_donor" 
                                                   id="is_available_donor" 
                                                   value="1" 
                                                   <?= !empty($profile['is_available_donor']) ? 'checked' : '' ?>>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="d-flex justify-content-end gap-2">
                                    <a href="<?= APP_URL ?>/dashboard.php" class="btn btn-light rounded-pill px-4">Cancel</a>
                                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-semibold shadow-sm">
                                        <i class="bi bi-check-circle me-1"></i> Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . '/views/includes/footer.php'; ?>
