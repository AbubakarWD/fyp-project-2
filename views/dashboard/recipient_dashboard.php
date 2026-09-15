<?php
/**
 * BloodLife — Recipient / Requester Dashboard View
 * Full-featured authenticated dashboard for blood requesters.
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> — <?= APP_NAME ?></title>

    <!-- Bootstrap 5.3 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- BloodLife Design System -->
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/design-system.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/dashboard.css">
</head>
<body>

<div class="bl-dash-wrapper">
    <!-- Authenticated Navigation Sidebar -->
    <?php require_once ROOT_PATH . '/views/includes/dash-sidebar.php'; ?>

    <!-- Main Dashboard Area -->
    <div class="bl-dash-main">
        <!-- Authenticated Topbar Header -->
        <?php require_once ROOT_PATH . '/views/includes/dash-header.php'; ?>

        <!-- Dashboard Content -->
        <main class="bl-dash-content">
            <?php require_once ROOT_PATH . '/views/includes/toast.php'; ?>

            <!-- Welcome & CTA Header -->
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
                <div>
                    <h1 class="h2 text-dark mb-1">Requester Overview</h1>
                    <p class="text-muted small mb-0">Manage emergency blood requests, track donor responses, and coordinate hospital logistics.</p>
                </div>

                <div>
                    <a href="<?= APP_URL ?>/request-create.php" class="bl-btn bl-btn-primary bl-btn-lg">
                        <i class="bi bi-plus-circle-fill"></i> Create Blood Request
                    </a>
                </div>
            </div>

            <!-- Stat Counters Grid -->
            <div class="row g-3 mb-4">
                <div class="col-md-3 col-sm-6">
                    <div class="bl-stat-card">
                        <div class="bl-stat-icon bl-stat-icon-amber">
                            <i class="bi bi-heart-pulse-fill"></i>
                        </div>
                        <div>
                            <div class="bl-stat-value"><?= e($activeRequestsCount) ?></div>
                            <div class="bl-stat-label">Active Requests</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="bl-stat-card">
                        <div class="bl-stat-icon bl-stat-icon-green">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <div>
                            <div class="bl-stat-value"><?= e($fulfilledRequestsCount) ?></div>
                            <div class="bl-stat-label">Fulfilled Requests</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="bl-stat-card">
                        <div class="bl-stat-icon bl-stat-icon-blue">
                            <i class="bi bi-chat-dots-fill"></i>
                        </div>
                        <div>
                            <div class="bl-stat-value"><?= e($totalResponsesReceived) ?></div>
                            <div class="bl-stat-label">Responses Received</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="bl-stat-card">
                        <div class="bl-stat-icon bl-stat-icon-red">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div>
                            <div class="bl-stat-value"><?= e($availableDonorsCount) ?></div>
                            <div class="bl-stat-label">Available Donors (<?= e($userProfile['city'] ?? 'City') ?>)</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Action Cards Bar -->
            <div class="row g-3 mb-4">
                <div class="col-md-3 col-6">
                    <a href="<?= APP_URL ?>/request-create.php" class="bl-card bl-card-sm text-center bl-card-interactive mb-0">
                        <i class="bi bi-plus-circle-fill text-danger fs-3 mb-2 d-block"></i>
                        <span class="fw-bold text-dark small">New Request</span>
                    </a>
                </div>
                <div class="col-md-3 col-6">
                    <a href="<?= APP_URL ?>/donors.php" class="bl-card bl-card-sm text-center bl-card-interactive mb-0">
                        <i class="bi bi-search text-primary fs-3 mb-2 d-block"></i>
                        <span class="fw-bold text-dark small">Find Donors</span>
                    </a>
                </div>
                <div class="col-md-3 col-6">
                    <a href="<?= APP_URL ?>/requests.php" class="bl-card bl-card-sm text-center bl-card-interactive mb-0">
                        <i class="bi bi-list-task text-success fs-3 mb-2 d-block"></i>
                        <span class="fw-bold text-dark small">All Requests</span>
                    </a>
                </div>
                <div class="col-md-3 col-6">
                    <a href="<?= APP_URL ?>/profile.php" class="bl-card bl-card-sm text-center bl-card-interactive mb-0">
                        <i class="bi bi-person-gear text-warning fs-3 mb-2 d-block"></i>
                        <span class="fw-bold text-dark small">Edit Profile</span>
                    </a>
                </div>
            </div>

            <!-- Main Grid: My Blood Requests & Requester Info -->
            <div class="row g-4">
                <div class="col-lg-8">
                    <!-- My Blood Requests Card -->
                    <div class="bl-card mb-4">
                        <div class="bl-card-header">
                            <h3 class="bl-card-title"><i class="bi bi-folder-fill text-primary me-2"></i> My Emergency Blood Requests</h3>
                            <a href="<?= APP_URL ?>/request-create.php" class="bl-btn bl-btn-primary bl-btn-sm">+ Create Request</a>
                        </div>
                        <div class="bl-card-body p-0">
                            <?php if (empty($myRequests)): ?>
                                <div class="p-5 text-center">
                                    <i class="bi bi-inbox-fill text-muted display-4 mb-3"></i>
                                    <h5 class="fw-bold text-dark">No Blood Requests Yet</h5>
                                    <p class="text-muted small mb-3">Submit your first request to alert matching voluntary donors instantly.</p>
                                    <a href="<?= APP_URL ?>/request-create.php" class="bl-btn bl-btn-primary">
                                        <i class="bi bi-plus-circle-fill"></i> Create Request Now
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table align-middle mb-0">
                                        <thead class="table-light">
                                            <tr class="small text-uppercase text-muted">
                                                <th>Patient / Hospital</th>
                                                <th>Blood</th>
                                                <th>Units</th>
                                                <th>Urgency</th>
                                                <th>Status</th>
                                                <th>Responses</th>
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($myRequests as $req): ?>
                                                <tr>
                                                    <td>
                                                        <div class="fw-bold text-dark"><?= e($req['patient_name']) ?></div>
                                                        <div class="small text-muted"><?= e($req['hospital_name']) ?>, <?= e($req['city']) ?></div>
                                                    </td>
                                                    <td>
                                                        <span class="bl-badge bl-badge-blood"><?= e($req['blood_group']) ?></span>
                                                    </td>
                                                    <td>
                                                        <span class="fw-bold text-dark"><?= e($req['units_fulfilled']) ?> / <?= e($req['units_required']) ?></span>
                                                    </td>
                                                    <td>
                                                        <?= FormatHelper::urgencyBadge($req['urgency_level']) ?>
                                                    </td>
                                                    <td>
                                                        <?= FormatHelper::statusBadge($req['status']) ?>
                                                    </td>
                                                    <td>
                                                        <span class="bl-badge bl-badge-secondary">
                                                            <i class="bi bi-chat-dots-fill me-1"></i><?= e($req['response_count'] ?? 0) ?>
                                                        </span>
                                                    </td>
                                                    <td class="text-end">
                                                        <div class="btn-group">
                                                            <button type="button" class="bl-btn bl-btn-outline bl-btn-sm" onclick="loadResponsesModal(<?= $req['id'] ?>, '<?= e($req['patient_name']) ?>')">
                                                                Responses
                                                            </button>
                                                            <?php if ($req['status'] === 'active' || $req['status'] === 'partially_fulfilled'): ?>
                                                                <button type="button" class="bl-btn bl-btn-light bl-btn-sm text-danger" onclick="cancelRequest(<?= $req['id'] ?>)">
                                                                    Cancel
                                                                </button>
                                                            <?php endif; ?>
                                                        </div>
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

                <!-- Requester Info & Notifications -->
                <div class="col-lg-4">
                    <div class="bl-card text-center mb-4">
                        <img src="<?= APP_URL ?>/assets/images/default_avatar.png" alt="<?= e($userProfile['full_name'] ?? $currentUser['username'] ?? 'Requester') ?>" class="bl-avatar mx-auto mb-3" style="width: 80px; height: 80px;">
                        <h4 class="h5 fw-bold text-dark mb-1"><?= e($userProfile['full_name'] ?? $currentUser['username'] ?? 'Requester') ?></h4>
                        <p class="text-muted small mb-2"><i class="bi bi-geo-alt-fill text-danger me-1"></i> <?= e($userProfile['city'] ?? 'Karachi') ?></p>
                        
                        <div class="d-flex justify-content-center gap-2 mb-3">
                            <span class="bl-badge bl-badge-primary">Requester Account</span>
                        </div>

                        <div class="bg-light p-3 rounded mb-3 text-start small">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Total Requests:</span>
                                <span class="fw-bold text-dark"><?= count($myRequests) ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Active Cases:</span>
                                <span class="fw-bold text-danger"><?= e($activeRequestsCount) ?></span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Contact Phone:</span>
                                <span class="fw-bold text-dark"><?= e($userProfile['phone_number'] ?? 'N/A') ?></span>
                            </div>
                        </div>

                        <a href="<?= APP_URL ?>/profile.php" class="bl-btn bl-btn-outline w-100 bl-btn-sm">
                            <i class="bi bi-gear-fill"></i> Manage Account
                        </a>
                    </div>

                    <!-- Recent Notifications -->
                    <div class="bl-card">
                        <h4 class="h5 fw-bold text-dark mb-3"><i class="bi bi-clock-history text-primary me-2"></i> Recent Activity</h4>
                        <?php if (empty($recentNotifications)): ?>
                            <p class="text-muted small mb-0">No recent activity.</p>
                        <?php else: ?>
                            <div class="d-flex flex-column gap-3">
                                <?php foreach ($recentNotifications as $notif): ?>
                                    <div class="border-bottom pb-2">
                                        <div class="fw-semibold text-dark small mb-1"><?= e($notif['title']) ?></div>
                                        <div class="text-muted fs-caption"><?= e($notif['message']) ?></div>
                                        <div class="text-muted fs-caption mt-1"><?= FormatHelper::timeAgo($notif['created_at']) ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Responses Modal -->
<div class="modal fade bl-modal" id="responsesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="bi bi-people-fill text-primary me-2"></i> Donor Responses for <span id="modalRequestPatientName" class="text-danger"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" id="responsesModalContent">
                <div class="p-4 text-center">
                    <div class="spinner-border text-danger" role="status"></div>
                    <p class="text-muted small mt-2">Loading donor responses...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="bl-btn bl-btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= APP_URL ?>/assets/js/app.js"></script>

<script>
// Load Responses Modal Content via AJAX or preloaded data
function loadResponsesModal(requestId, patientName) {
    document.getElementById('modalRequestPatientName').textContent = patientName;
    const content = document.getElementById('responsesModalContent');

    content.innerHTML = `
        <div class="p-4 text-center">
            <div class="spinner-border text-danger" role="status"></div>
            <p class="text-muted small mt-2">Loading donor responses...</p>
        </div>
    `;

    const modal = new bootstrap.Modal(document.getElementById('responsesModal'));
    modal.show();

    fetch('<?= APP_URL ?>/api/request-responses.php?request_id=' + requestId)
    .then(res => res.json())
    .then(data => {
        if (data.success && data.responses.length > 0) {
            let html = '<div class="list-group list-group-flush">';
            data.responses.forEach(resp => {
                let statusBadge = '<span class="bl-badge bl-badge-warning">Pending</span>';
                if (resp.status === 'accepted') statusBadge = '<span class="bl-badge bl-badge-primary">Accepted</span>';
                if (resp.status === 'completed') statusBadge = '<span class="bl-badge bl-badge-success">Completed</span>';
                if (resp.status === 'rejected') statusBadge = '<span class="bl-badge bl-badge-danger">Declined</span>';

                html += `
                    <div class="list-group-item p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="bl-badge bl-badge-blood">${resp.donor_blood_group}</span>
                                <strong class="text-dark">${resp.donor_name}</strong>
                                <small class="text-muted">(${resp.donor_city})</small>
                            </div>
                            <div>${statusBadge}</div>
                        </div>
                        <p class="text-muted small mb-2"><i class="bi bi-telephone-fill text-danger me-1"></i> Phone: <strong>${resp.donor_phone}</strong></p>
                        ${resp.donor_note ? `<p class="bg-light p-2 rounded small text-dark mb-2"><em>"${resp.donor_note}"</em></p>` : ''}
                        
                        <div class="d-flex gap-2 justify-content-end">
                            ${resp.status === 'pending' ? `
                                <button class="bl-btn bl-btn-success bl-btn-sm" onclick="updateResponseStatus(${resp.id}, 'accepted')">Accept</button>
                                <button class="bl-btn bl-btn-light bl-btn-sm text-danger" onclick="updateResponseStatus(${resp.id}, 'rejected')">Decline</button>
                            ` : ''}
                            ${resp.status === 'accepted' ? `
                                <button class="bl-btn bl-btn-primary bl-btn-sm" onclick="updateResponseStatus(${resp.id}, 'completed')"><i class="bi bi-check-lg"></i> Mark Donation Completed</button>
                            ` : ''}
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            content.innerHTML = html;
        } else {
            content.innerHTML = `
                <div class="p-4 text-center">
                    <i class="bi bi-chat-dots-fill text-muted display-6 mb-2"></i>
                    <p class="text-muted small mb-0">No donor responses received for this request yet.</p>
                </div>
            `;
        }
    })
    .catch(err => {
        content.innerHTML = '<div class="p-4 text-center text-danger">Error loading donor responses.</div>';
    });
}

// Update Response Status AJAX
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
            bootstrap.Modal.getInstance(document.getElementById('responsesModal')).hide();
            setTimeout(() => location.reload(), 1200);
        } else {
            showToast(data.message, 'danger');
        }
    });
}

// Cancel Request AJAX
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
</body>
</html>
