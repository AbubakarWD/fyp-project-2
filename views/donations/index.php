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

        <!-- Main Body -->
        <div class="col-lg-9">
            <!-- Header Banner -->
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <div>
                    <h2 class="h3 fw-bold mb-1 text-slate-800">Donation History & Impact Log</h2>
                    <p class="text-muted small mb-0">Track your life-saving blood donations, hospital logs, and total impact count.</p>
                </div>
                <button type="button" class="btn btn-danger btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#logDonationModal">
                    <i class="bi bi-plus-circle me-1"></i> Log New Donation
                </button>
            </div>

            <!-- Stats Overview Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="bl-card bl-card-sm mb-0">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-danger bg-opacity-10 text-danger p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="bi bi-heart-pulse fs-4"></i>
                            </div>
                            <div>
                                <div class="text-muted small">Total Donations</div>
                                <div class="h4 fw-bold mb-0 text-slate-800"><?= (int)($profile['total_donations_count'] ?? count($donations)) ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="bl-card bl-card-sm mb-0">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-success bg-opacity-10 text-success p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="bi bi-people-fill fs-4"></i>
                            </div>
                            <div>
                                <div class="text-muted small">Lives Potentially Saved</div>
                                <div class="h4 fw-bold mb-0 text-slate-800"><?= (int)($profile['total_donations_count'] ?? count($donations)) * 3 ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="bl-card bl-card-sm mb-0">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="bi bi-calendar-event fs-4"></i>
                            </div>
                            <div>
                                <div class="text-muted small">Last Donation Date</div>
                                <div class="h6 fw-bold mb-0 text-slate-800">
                                    <?= !empty($profile['last_donation_date']) ? FormatHelper::formatDate($profile['last_donation_date']) : (!empty($donations[0]['donation_date']) ? FormatHelper::formatDate($donations[0]['donation_date']) : 'None recorded') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Donation History List / Timeline -->
            <div class="bl-card mb-0">
                <div class="bl-card-header">
                    <h5 class="fw-bold mb-0 text-slate-800"><i class="bi bi-journal-medical text-danger me-2"></i> Verified Donation Records</h5>
                </div>
                <div class="card-body p-4">
                    <?php if (empty($donations)): ?>
                        <div class="text-center py-5">
                            <div class="mb-3 text-muted display-4"><i class="bi bi-journal-x"></i></div>
                            <h5 class="fw-semibold text-slate-700">No Donation Records Found</h5>
                            <p class="text-muted small max-w-md mx-auto">You haven't logged any blood donations yet. When you complete a donation at a hospital or blood bank, log it here to keep your record updated!</p>
                            <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-4 mt-2" data-bs-toggle="modal" data-bs-target="#logDonationModal">
                                Log Your First Donation
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Date</th>
                                        <th>Facility & Hospital</th>
                                        <th>City</th>
                                        <th>Units</th>
                                        <th>Patient Link</th>
                                        <th>Status</th>
                                        <th class="pe-3">Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($donations as $rec): ?>
                                        <tr>
                                            <td class="ps-3 fw-semibold text-slate-800">
                                                <?= FormatHelper::formatDate($rec['donation_date']) ?>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-slate-800"><?= SecurityHelper::sanitizeOutput($rec['facility_name']) ?></div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border"><?= SecurityHelper::sanitizeOutput($rec['city']) ?></span>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-danger"><?= (int)$rec['units_donated'] ?> Unit(s)</span>
                                            </td>
                                            <td>
                                                <?php if (!empty($rec['patient_name'])): ?>
                                                    <span class="text-slate-700"><i class="bi bi-person me-1"></i><?= SecurityHelper::sanitizeOutput($rec['patient_name']) ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted small">Direct / Walk-in</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1">
                                                    <i class="bi bi-patch-check me-1"></i> Verified
                                                </span>
                                            </td>
                                            <td class="pe-3 text-muted small max-w-xs">
                                                <?= !empty($rec['notes']) ? SecurityHelper::sanitizeOutput($rec['notes']) : '—' ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Log New Donation -->
<div class="modal fade" id="logDonationModal" tabindex="-1" aria-labelledby="logDonationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="bg-crimson-gradient p-4 text-white">
                <h5 class="modal-title fw-bold" id="logDonationModalLabel">
                    <i class="bi bi-plus-circle me-2"></i> Log Verified Blood Donation
                </h5>
                <p class="text-white-50 small mb-0">Record details of your recent blood donation to update your donor record.</p>
            </div>
            <form action="<?= APP_URL ?>/donation_history.php?action=store" method="POST">
                <div class="modal-body p-4">
                    <?= SecurityHelper::getCsrfTokenInput() ?>

                    <!-- Facility Name -->
                    <div class="mb-3">
                        <label for="facility_name" class="form-label fw-semibold text-slate-700">Hospital / Blood Bank Facility <span class="text-danger">*</span></label>
                        <input type="text" id="facility_name" name="facility_name" class="form-control rounded-3" placeholder="e.g. Aga Khan University Hospital, Indus Hospital" required>
                    </div>

                    <!-- City & Donation Date -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="city" class="form-label fw-semibold text-slate-700">City <span class="text-danger">*</span></label>
                            <input type="text" id="city" name="city" class="form-control rounded-3" value="<?= SecurityHelper::sanitizeOutput($profile['city'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="donation_date" class="form-label fw-semibold text-slate-700">Donation Date <span class="text-danger">*</span></label>
                            <input type="date" id="donation_date" name="donation_date" class="form-control rounded-3" value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>

                    <!-- Units Donated -->
                    <div class="mb-3">
                        <label for="units_donated" class="form-label fw-semibold text-slate-700">Units Donated</label>
                        <select id="units_donated" name="units_donated" class="form-select rounded-3">
                            <option value="1" selected>1 Unit (350 - 450 ml)</option>
                            <option value="2">2 Units (Double Red Blood Cell)</option>
                        </select>
                    </div>

                    <!-- Notes -->
                    <div class="mb-3">
                        <label for="notes" class="form-label fw-semibold text-slate-700">Notes / Certificate Details</label>
                        <textarea id="notes" name="notes" rows="2" class="form-control rounded-3" placeholder="Add any extra details, reference IDs, or notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3 border-top-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-semibold shadow-sm">
                        <i class="bi bi-check-circle me-1"></i> Save Donation Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . '/views/includes/footer.php'; ?>
