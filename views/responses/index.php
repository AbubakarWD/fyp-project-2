<?php
/**
 * BloodLife — Donor Responses & Connection Hub View
 */
require_once ROOT_PATH . '/views/includes/header.php';
?>

<main class="py-5">
    <div class="container">
        <div class="bl-card bl-card-lg mb-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4 pb-3 border-bottom">
                <div>
                    <h1 class="h2 text-dark mb-1"><i class="bi bi-people-fill text-danger me-2"></i> Response & Connection Hub</h1>
                    <p class="text-muted small mb-0">Track all your submitted donor responses and manage incoming connections from voluntary donors.</p>
                </div>
                <a href="<?= APP_URL ?>/dashboard.php" class="bl-btn bl-btn-light bl-btn-sm"><i class="bi bi-speedometer2"></i> Dashboard</a>
            </div>

            <!-- Tab Nav -->
            <ul class="nav nav-pills mb-4" id="responseTabs" role="tablist">
                <?php if ($userRole === 'donor' || $userRole === 'both'): ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold" id="submitted-tab" data-bs-toggle="tab" data-bs-target="#submitted-tab-pane" type="button" role="tab">
                            <i class="bi bi-send-fill me-1"></i> My Submitted Responses (<?= count($mySubmittedResponses) ?>)
                        </button>
                    </li>
                <?php endif; ?>
                <?php if ($userRole === 'requester' || $userRole === 'both'): ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= $userRole === 'requester' ? 'active' : '' ?> fw-bold" id="received-tab" data-bs-toggle="tab" data-bs-target="#received-tab-pane" type="button" role="tab">
                            <i class="bi bi-inbox-fill me-1"></i> Responses Received (<?= count($receivedResponses) ?>)
                        </button>
                    </li>
                <?php endif; ?>
            </ul>

            <div class="tab-content" id="responseTabsContent">
                <!-- Tab 1: Submitted Responses (Donor) -->
                <?php if ($userRole === 'donor' || $userRole === 'both'): ?>
                    <div class="tab-pane fade show active" id="submitted-tab-pane" role="tabpanel">
                        <?php if (empty($mySubmittedResponses)): ?>
                            <div class="p-5 text-center">
                                <i class="bi bi-chat-heart text-muted display-4 mb-3"></i>
                                <h5 class="fw-bold text-dark">No Submitted Responses Yet</h5>
                                <p class="text-muted small mb-3">Browse emergency blood requests and click "I Can Donate" to offer assistance.</p>
                                <a href="<?= APP_URL ?>/requests.php" class="bl-btn bl-btn-primary">Browse Blood Requests</a>
                            </div>
                        <?php else: ?>
                            <div class="row g-3">
                                <?php foreach ($mySubmittedResponses as $resp): ?>
                                    <div class="col-md-6">
                                        <div class="bl-card mb-0">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <div class="fw-bold text-dark fs-5"><?= e($resp['patient_name']) ?></div>
                                                <?= FormatHelper::statusBadge($resp['status']) ?>
                                            </div>
                                            <p class="text-muted small mb-2"><i class="bi bi-hospital text-danger me-1"></i> <?= e($resp['hospital_name']) ?> (<?= e($resp['city']) ?>)</p>
                                            <div class="bg-light p-3 rounded mb-3 text-dark small">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <span>Units Offered:</span>
                                                    <strong class="text-danger"><?= e($resp['units_offered']) ?> Unit(s)</strong>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <span>Requester:</span>
                                                    <strong><?= e($resp['requester_name']) ?></strong>
                                                </div>
                                            </div>
                                            <?php if (!empty($resp['donor_note'])): ?>
                                                <p class="text-muted fs-caption mb-3"><em>"<?= e($resp['donor_note']) ?>"</em></p>
                                            <?php endif; ?>
                                            <div class="d-flex justify-content-between align-items-center border-top pt-2">
                                                <small class="text-muted"><?= FormatHelper::timeAgo($resp['created_at']) ?></small>
                                                <?php if ($resp['status'] === 'pending'): ?>
                                                    <button type="button" class="bl-btn bl-btn-light bl-btn-sm text-danger" onclick="cancelDonorResponse(<?= $resp['id'] ?>)">
                                                        Cancel Response
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Tab 2: Received Responses (Requester) -->
                <?php if ($userRole === 'requester' || $userRole === 'both'): ?>
                    <div class="tab-pane fade <?= $userRole === 'requester' ? 'show active' : '' ?>" id="received-tab-pane" role="tabpanel">
                        <?php if (empty($receivedResponses)): ?>
                            <div class="p-5 text-center">
                                <i class="bi bi-inbox-fill text-muted display-4 mb-3"></i>
                                <h5 class="fw-bold text-dark">No Donor Responses Received</h5>
                                <p class="text-muted small mb-0">When voluntary donors respond to your requests, they will appear here.</p>
                            </div>
                        <?php else: ?>
                            <div class="row g-3">
                                <?php foreach ($receivedResponses as $resp): ?>
                                    <div class="col-md-6">
                                        <div class="bl-card mb-0">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="bl-badge bl-badge-blood"><?= e($resp['donor_blood_group']) ?></span>
                                                    <strong class="text-dark"><?= e($resp['donor_name']) ?></strong>
                                                </div>
                                                <?= FormatHelper::statusBadge($resp['status']) ?>
                                            </div>
                                            <p class="text-muted small mb-2"><i class="bi bi-telephone-fill text-danger me-1"></i> Phone: <strong><?= e($resp['donor_phone']) ?></strong></p>
                                            <p class="text-muted small mb-3">For Patient: <strong><?= e($resp['patient_name']) ?></strong> at <?= e($resp['hospital_name']) ?></p>
                                            
                                            <?php if (!empty($resp['donor_note'])): ?>
                                                <p class="bg-light p-2 rounded small text-dark mb-3"><em>"<?= e($resp['donor_note']) ?>"</em></p>
                                            <?php endif; ?>

                                            <div class="d-flex gap-2 justify-content-end border-top pt-2">
                                                <?php if ($resp['status'] === 'pending'): ?>
                                                    <button class="bl-btn bl-btn-success bl-btn-sm" onclick="updateResponseStatus(<?= $resp['id'] ?>, 'accepted')">Accept</button>
                                                    <button class="bl-btn bl-btn-light bl-btn-sm text-danger" onclick="updateResponseStatus(<?= $resp['id'] ?>, 'rejected')">Decline</button>
                                                <?php endif; ?>
                                                <?php if ($resp['status'] === 'accepted'): ?>
                                                    <button class="bl-btn bl-btn-primary bl-btn-sm" onclick="updateResponseStatus(<?= $resp['id'] ?>, 'completed')"><i class="bi bi-check-lg"></i> Mark Completed</button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<script>
function cancelDonorResponse(responseId) {
    if (!confirm('Are you sure you want to cancel your response?')) return;

    fetch('<?= APP_URL ?>/api/donor-cancel-response.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `response_id=${responseId}`
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
</script>

<?php require_once ROOT_PATH . '/views/includes/footer.php'; ?>
