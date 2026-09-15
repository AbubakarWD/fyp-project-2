<?php
/**
 * BloodLife — Emergency Blood Requests Feed & Search View
 */
require_once ROOT_PATH . '/views/includes/header.php';
?>

<main class="py-5">
    <div class="container">
        <!-- Search & Filter Header -->
        <div class="bl-card bl-card-lg mb-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4 pb-3 border-bottom">
                <div>
                    <h1 class="h2 text-dark mb-1"><i class="bi bi-heart-pulse-fill text-danger me-2"></i> Emergency Blood Requests</h1>
                    <p class="text-muted small mb-0">Browse active emergency calls, filter by ABO group and urgency level, and respond to save lives.</p>
                </div>

                <?php if (SessionHelper::isLoggedIn()): ?>
                    <a href="<?= APP_URL ?>/request-create.php" class="bl-btn bl-btn-primary">
                        <i class="bi bi-plus-circle-fill"></i> Create Request
                    </a>
                <?php endif; ?>
            </div>

            <!-- Filter Controls Form -->
            <form action="<?= APP_URL ?>/requests.php" method="GET" class="row g-3">
                <div class="col-lg-3 col-md-6">
                    <label class="bl-form-label">Blood Group</label>
                    <select name="blood_group_id" class="bl-form-select">
                        <option value="">All Blood Groups</option>
                        <?php foreach ($allBloodGroups as $bg): ?>
                            <option value="<?= $bg['id'] ?>" <?= $bloodGroupId == $bg['id'] ? 'selected' : '' ?>>
                                <?= e($bg['code']) ?> — <?= e($bg['display_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-lg-3 col-md-6">
                    <label class="bl-form-label">City / Location</label>
                    <select name="city" class="bl-form-select">
                        <option value="">All Cities</option>
                        <option value="Karachi" <?= $city === 'Karachi' ? 'selected' : '' ?>>Karachi</option>
                        <option value="Lahore" <?= $city === 'Lahore' ? 'selected' : '' ?>>Lahore</option>
                        <option value="Islamabad" <?= $city === 'Islamabad' ? 'selected' : '' ?>>Islamabad</option>
                        <option value="Rawalpindi" <?= $city === 'Rawalpindi' ? 'selected' : '' ?>>Rawalpindi</option>
                        <option value="Faisalabad" <?= $city === 'Faisalabad' ? 'selected' : '' ?>>Faisalabad</option>
                        <option value="Multan" <?= $city === 'Multan' ? 'selected' : '' ?>>Multan</option>
                        <option value="Peshawar" <?= $city === 'Peshawar' ? 'selected' : '' ?>>Peshawar</option>
                        <option value="Quetta" <?= $city === 'Quetta' ? 'selected' : '' ?>>Quetta</option>
                    </select>
                </div>

                <div class="col-lg-3 col-md-6">
                    <label class="bl-form-label">Urgency Level</label>
                    <select name="urgency" class="bl-form-select">
                        <option value="">All Urgency Levels</option>
                        <option value="critical" <?= $urgency === 'critical' ? 'selected' : '' ?>>Critical</option>
                        <option value="high" <?= $urgency === 'high' ? 'selected' : '' ?>>High</option>
                        <option value="medium" <?= $urgency === 'medium' ? 'selected' : '' ?>>Medium</option>
                        <option value="low" <?= $urgency === 'low' ? 'selected' : '' ?>>Low</option>
                    </select>
                </div>

                <div class="col-lg-3 col-md-6">
                    <label class="bl-form-label">Request Status</label>
                    <select name="status" class="bl-form-select">
                        <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active Requests Only</option>
                        <option value="partially_fulfilled" <?= $status === 'partially_fulfilled' ? 'selected' : '' ?>>Partially Fulfilled</option>
                        <option value="fulfilled" <?= $status === 'fulfilled' ? 'selected' : '' ?>>Fulfilled</option>
                        <option value="cancelled" <?= $status === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>

                <div class="col-12 d-flex justify-content-end gap-2 pt-2">
                    <a href="<?= APP_URL ?>/requests.php" class="bl-btn bl-btn-light">Reset Filters</a>
                    <button type="submit" class="bl-btn bl-btn-primary">
                        <i class="bi bi-funnel-fill"></i> Filter Requests
                    </button>
                </div>
            </form>
        </div>

        <!-- Requests Grid -->
        <?php if (empty($requests)): ?>
            <div class="bl-card bl-card-lg text-center">
                <i class="bi bi-heart-pulse text-muted display-3 mb-3"></i>
                <h3 class="h4 text-dark mb-2">No Matching Blood Requests</h3>
                <p class="text-muted small mb-4">There are currently no active blood requests matching your search criteria.</p>
                <a href="<?= APP_URL ?>/requests.php" class="bl-btn bl-btn-primary">View All Requests</a>
            </div>
        <?php else: ?>
            <div class="row g-4 mb-4">
                <?php foreach ($requests as $req): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="bl-card h-100 mb-0 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                    <span class="bl-badge bl-badge-blood"><?= e($req['blood_group']) ?></span>
                                    <?= FormatHelper::urgencyBadge($req['urgency_level']) ?>
                                </div>

                                <h4 class="h5 fw-bold text-dark mb-1"><?= e($req['patient_name']) ?></h4>
                                <p class="text-muted small mb-2"><i class="bi bi-hospital text-danger me-1"></i> <?= e($req['hospital_name']) ?></p>
                                <p class="text-muted small mb-3"><i class="bi bi-geo-alt-fill text-danger me-1"></i> <?= e($req['city']) ?></p>

                                <div class="bg-light p-3 rounded mb-3">
                                    <div class="d-flex justify-content-between text-muted small mb-1">
                                        <span>Units Fulfilled:</span>
                                        <span class="fw-bold text-dark"><?= e($req['units_fulfilled']) ?> of <?= e($req['units_required']) ?> Bags</span>
                                    </div>
                                    <div class="progress mb-2" style="height: 6px;">
                                        <?php $pct = min(100, round(($req['units_fulfilled'] / max(1, $req['units_required'])) * 100)); ?>
                                        <div class="progress-bar bg-success" role="progressbar" style="width: <?= $pct ?>%"></div>
                                    </div>
                                    <div class="d-flex justify-content-between text-muted fs-caption">
                                        <span>Needed By: <strong><?= FormatHelper::formatDate($req['required_date']) ?></strong></span>
                                        <span><?= FormatHelper::statusBadge($req['status']) ?></span>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-2 pt-2 border-top">
                                <a href="<?= APP_URL ?>/request-detail.php?id=<?= $req['id'] ?>" class="bl-btn bl-btn-outline w-100 bl-btn-sm">
                                    <i class="bi bi-info-circle"></i> View Details
                                </a>
                                <?php if ($req['status'] === 'active' || $req['status'] === 'partially_fulfilled'): ?>
                                    <button type="button" class="bl-btn bl-btn-primary bl-btn-sm flex-shrink-0" 
                                            onclick="openRespondModal(<?= $req['id'] ?>, '<?= e($req['patient_name']) ?>', '<?= e($req['blood_group']) ?>')">
                                        <i class="bi bi-heart-fill"></i> Respond
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination Controls -->
            <?php if ($totalPages > 1): ?>
                <nav class="d-flex justify-content-center">
                    <ul class="pagination">
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= APP_URL ?>/requests.php?page=<?= $page - 1 ?>&city=<?= urlencode($city) ?>&urgency=<?= urlencode($urgency) ?>&status=<?= urlencode($status) ?>">Previous</a>
                        </li>
                        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                            <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                                <a class="page-link" href="<?= APP_URL ?>/requests.php?page=<?= $p ?>&city=<?= urlencode($city) ?>&urgency=<?= urlencode($urgency) ?>&status=<?= urlencode($status) ?>"><?= $p ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= APP_URL ?>/requests.php?page=<?= $page + 1 ?>&city=<?= urlencode($city) ?>&urgency=<?= urlencode($urgency) ?>&status=<?= urlencode($status) ?>">Next</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</main>

<!-- Donor Respond Modal -->
<div class="modal fade bl-modal" id="respondModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-heart-fill text-danger me-2"></i> Respond to Blood Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php if (SessionHelper::isLoggedIn()): ?>
                <form id="respondForm" onsubmit="submitDonorResponse(event)">
                    <div class="modal-body">
                        <input type="hidden" id="modalRequestId" name="request_id" value="">
                        
                        <div class="p-3 bg-light rounded mb-3">
                            <div class="small text-muted">Patient: <strong id="modalPatientName" class="text-dark"></strong></div>
                            <div class="small text-muted">Required Blood Group: <strong id="modalBloodGroup" class="text-danger"></strong></div>
                        </div>

                        <div class="mb-3">
                            <label class="bl-form-label">Units You Can Offer</label>
                            <select name="units_offered" class="bl-form-select" required>
                                <option value="1">1 Unit (Standard Bag)</option>
                                <option value="2">2 Units</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="bl-form-label">Note for Requester (Optional)</label>
                            <textarea name="donor_note" class="bl-form-control" rows="3" placeholder="e.g. I am available today and can reach the hospital by 4 PM."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="bl-btn bl-btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="bl-btn bl-btn-primary"><i class="bi bi-send-fill"></i> Submit Response</button>
                    </div>
                </form>
            <?php else: ?>
                <div class="modal-body text-center p-4">
                    <i class="bi bi-lock-fill text-warning display-4 mb-3"></i>
                    <h5 class="fw-bold text-dark mb-2">Login Required</h5>
                    <p class="text-muted small mb-4">Please log in to your BloodLife donor account to respond to emergency requests.</p>
                    <a href="<?= APP_URL ?>/login.php" class="bl-btn bl-btn-primary w-100">Log In Now</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function openRespondModal(requestId, patientName, bloodGroup) {
    const reqInput = document.getElementById('modalRequestId');
    if (reqInput) reqInput.value = requestId;
    const nameEl = document.getElementById('modalPatientName');
    if (nameEl) nameEl.textContent = patientName;
    const bgEl = document.getElementById('modalBloodGroup');
    if (bgEl) bgEl.textContent = bloodGroup;

    const modal = new bootstrap.Modal(document.getElementById('respondModal'));
    modal.show();
}

function submitDonorResponse(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);

    fetch('<?= APP_URL ?>/api/respond.php', {
        method: 'POST',
        body: new URLSearchParams(formData)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            bootstrap.Modal.getInstance(document.getElementById('respondModal')).hide();
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast(data.message, 'danger');
        }
    })
    .catch(err => showToast('Error submitting response.', 'danger'));
}
</script>

<?php require_once ROOT_PATH . '/views/includes/footer.php'; ?>
