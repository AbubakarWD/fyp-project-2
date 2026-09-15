<?php
/**
 * BloodLife — Blood Request Detailed View
 */
require_once ROOT_PATH . '/views/includes/header.php';
?>

<main class="py-5">
    <div class="container">
        <!-- Back Navigation -->
        <div class="mb-3">
            <a href="<?= APP_URL ?>/requests.php" class="text-secondary small fw-bold">
                <i class="bi bi-arrow-left"></i> Back to All Requests
            </a>
        </div>

        <div class="row g-4">
            <!-- Main Details Area -->
            <div class="col-lg-8">
                <div class="bl-card bl-card-lg mb-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4 pb-3 border-bottom">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="bl-badge bl-badge-blood"><?= e($request['blood_group']) ?></span>
                                <?= FormatHelper::urgencyBadge($request['urgency_level']) ?>
                                <?= FormatHelper::statusBadge($request['status']) ?>
                            </div>
                            <h1 class="h2 text-dark mb-1"><?= e($request['patient_name']) ?></h1>
                            <p class="text-muted small mb-0"><i class="bi bi-geo-alt-fill text-danger me-1"></i> <?= e($request['city']) ?></p>
                        </div>

                        <?php if ($isOwner): ?>
                            <div class="d-flex gap-2">
                                <a href="<?= APP_URL ?>/request-edit.php?id=<?= $request['id'] ?>" class="bl-btn bl-btn-outline bl-btn-sm">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <?php if ($request['status'] === 'active' || $request['status'] === 'partially_fulfilled'): ?>
                                    <button class="bl-btn bl-btn-light bl-btn-sm text-danger" onclick="cancelRequest(<?= $request['id'] ?>)">
                                        Cancel Request
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Progress Bar & Units Info -->
                    <div class="bg-light p-4 rounded-3 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold text-dark">Required Units Fulfilled:</span>
                            <span class="fw-extrabold text-danger fs-5"><?= e($request['units_fulfilled']) ?> / <?= e($request['units_required']) ?> Bags</span>
                        </div>
                        <?php $pct = min(100, round(($request['units_fulfilled'] / max(1, $request['units_required'])) * 100)); ?>
                        <div class="progress mb-2" style="height: 10px;">
                            <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?= $pct ?>%"></div>
                        </div>
                        <div class="d-flex justify-content-between text-muted small">
                            <span>Status: <?= FormatHelper::statusBadge($request['status']) ?></span>
                            <span>Required By: <strong><?= FormatHelper::formatDate($request['required_date']) ?></strong></span>
                        </div>
                    </div>

                    <!-- Hospital & Medical Details Grid -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 border rounded">
                                <div class="text-muted small mb-1"><i class="bi bi-hospital text-danger me-1"></i> Hospital Name</div>
                                <div class="fw-bold text-dark"><?= e($request['hospital_name']) ?></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 border rounded">
                                <div class="text-muted small mb-1"><i class="bi bi-geo-alt text-danger me-1"></i> Hospital Address</div>
                                <div class="fw-bold text-dark"><?= e($request['hospital_address'] ?: $request['city']) ?></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 border rounded">
                                <div class="text-muted small mb-1"><i class="bi bi-telephone text-danger me-1"></i> Contact Phone</div>
                                <div class="fw-bold text-dark">
                                    <?php if (SessionHelper::isLoggedIn()): ?>
                                        <a href="tel:<?= e($request['contact_number']) ?>" class="text-danger text-decoration-none"><?= e($request['contact_number']) ?></a>
                                    <?php else: ?>
                                        <span class="text-muted"><?= substr($request['contact_number'], 0, 6) ?>***** (<a href="<?= APP_URL ?>/login.php" class="text-danger">Log in to view</a>)</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 border rounded">
                                <div class="text-muted small mb-1"><i class="bi bi-calendar-event text-danger me-1"></i> Required Date</div>
                                <div class="fw-bold text-dark"><?= FormatHelper::formatDate($request['required_date']) ?></div>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($request['medical_reason'])): ?>
                        <div class="mb-4">
                            <h5 class="h6 fw-bold text-dark mb-2">Medical Reason / Notes</h5>
                            <div class="p-3 bg-light rounded text-dark small">
                                <?= e($request['medical_reason']) ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Donor Action Section -->
                    <?php if (!$isOwner): ?>
                        <div class="p-4 bg-primary-subtle border border-primary-subtle rounded-3 d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div>
                                <h5 class="fw-bold text-dark mb-1">Can you donate for patient <?= e($request['patient_name']) ?>?</h5>
                                <p class="text-muted small mb-0">Submit your response to connect directly with the patient's family.</p>
                            </div>
                            <?php if ($userResponse): ?>
                                <span class="bl-badge bl-badge-success p-2 fs-6">
                                    <i class="bi bi-check-circle-fill me-1"></i> Response Submitted (<?= ucfirst($userResponse['status']) ?>)
                                </span>
                            <?php else: ?>
                                <button type="button" class="bl-btn bl-btn-primary bl-btn-lg" onclick="openRespondModal(<?= $request['id'] ?>, '<?= e($request['patient_name']) ?>', '<?= e($request['blood_group']) ?>')">
                                    <i class="bi bi-heart-fill"></i> I Can Donate
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Responses Section (For Request Owner) -->
                <?php if ($isOwner): ?>
                    <div class="bl-card">
                        <h4 class="h5 fw-bold text-dark mb-3"><i class="bi bi-people-fill text-danger me-2"></i> Donor Responses Received (<?= count($responses) ?>)</h4>
                        
                        <?php if (empty($responses)): ?>
                            <div class="p-4 text-center">
                                <i class="bi bi-inbox-fill text-muted fs-1 mb-2 d-block"></i>
                                <p class="text-muted small mb-0">No donor responses received yet. Automated notifications have been dispatched to local donors.</p>
                            </div>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($responses as $resp): ?>
                                    <div class="list-group-item p-3">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="bl-badge bl-badge-blood"><?= e($resp['donor_blood_group']) ?></span>
                                                <strong class="text-dark"><?= e($resp['donor_name']) ?></strong>
                                                <small class="text-muted">(<?= e($resp['donor_city']) ?>)</small>
                                            </div>
                                            <div>
                                                <?= FormatHelper::statusBadge($resp['status']) ?>
                                            </div>
                                        </div>
                                        <p class="text-muted small mb-2"><i class="bi bi-telephone-fill text-danger me-1"></i> Phone: <strong><?= e($resp['donor_phone']) ?></strong></p>
                                        <?php if (!empty($resp['donor_note'])): ?>
                                            <p class="bg-light p-2 rounded small text-dark mb-2"><em>"<?= e($resp['donor_note']) ?>"</em></p>
                                        <?php endif; ?>

                                        <div class="d-flex gap-2 justify-content-end">
                                            <?php if ($resp['status'] === 'pending'): ?>
                                                <button class="bl-btn bl-btn-success bl-btn-sm" onclick="updateResponseStatus(<?= $resp['id'] ?>, 'accepted')">Accept</button>
                                                <button class="bl-btn bl-btn-light bl-btn-sm text-danger" onclick="updateResponseStatus(<?= $resp['id'] ?>, 'rejected')">Decline</button>
                                            <?php endif; ?>
                                            <?php if ($resp['status'] === 'accepted'): ?>
                                                <button class="bl-btn bl-btn-primary bl-btn-sm" onclick="updateResponseStatus(<?= $resp['id'] ?>, 'completed')"><i class="bi bi-check-lg"></i> Mark Donation Completed</button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Requester Profile Sidebar Widget -->
            <div class="col-lg-4">
                <div class="bl-card text-center mb-0">
                    <img src="<?= APP_URL ?>/assets/images/default_avatar.png" alt="<?= e($request['requester_name']) ?>" class="bl-avatar mx-auto mb-3" style="width: 80px; height: 80px;">
                    <h4 class="h5 fw-bold text-dark mb-1"><?= e($request['requester_name']) ?></h4>
                    <p class="text-muted small mb-3"><i class="bi bi-geo-alt-fill text-danger me-1"></i> <?= e($request['city']) ?></p>
                    
                    <div class="p-3 bg-light rounded text-start small mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Posted:</span>
                            <span class="fw-bold text-dark"><?= FormatHelper::timeAgo($request['created_at']) ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Contact Person:</span>
                            <span class="fw-bold text-dark"><?= e($request['requester_name']) ?></span>
                        </div>
                    </div>

                    <a href="<?= APP_URL ?>/donors.php" class="bl-btn bl-btn-outline w-100 bl-btn-sm">
                        <i class="bi bi-search"></i> Find More Donors
                    </a>
                </div>
            </div>
        </div>
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
                            <textarea name="donor_note" class="bl-form-control" rows="3" placeholder="e.g. I am available today in Karachi and can reach the hospital by 4 PM."></textarea>
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
    });
}

function updateResponseStatus(responseId, status) {
    fetch('<?= APP_URL ?>/api/response-status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `response_id=${responseId}&status=${status}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 1200);
        } else {
            showToast(data.message, 'danger');
        }
    });
}

function cancelRequest(requestId) {
    if (!confirm('Are you sure you want to cancel this blood request?')) return;

    fetch('<?= APP_URL ?>/api/request-status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `request_id=${requestId}&status=cancelled`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 1200);
        } else {
            showToast(data.message, 'danger');
        }
    });
}
</script>

<?php require_once ROOT_PATH . '/views/includes/footer.php'; ?>
