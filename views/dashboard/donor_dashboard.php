<?php
/**
 * BloodLife — Donor Dashboard View
 * Full-featured authenticated dashboard for voluntary blood donors.
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

            <!-- Welcome & Availability Toggle Header -->
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
                <div>
                    <h1 class="h2 text-dark mb-1">Donor Overview</h1>
                    <p class="text-muted small mb-0">Manage your availability, view matching emergency requests, and track your life-saving impact.</p>
                </div>

                <!-- Real-Time Availability Switch -->
                <div class="bg-white p-3 rounded-3 border shadow-sm d-flex align-items-center gap-3">
                    <div>
                        <div class="fw-bold text-dark small">Donor Availability</div>
                        <div id="availabilityStatusLabel" class="small text-muted">
                            <?= !empty($userProfile['is_available_donor']) ? '<span class="text-success fw-bold"><i class="bi bi-circle-fill me-1"></i> Available to Donate</span>' : '<span class="text-danger fw-bold"><i class="bi bi-dash-circle-fill me-1"></i> Currently Unavailable</span>' ?>
                        </div>
                    </div>
                    <div class="form-check form-switch fs-4">
                        <input class="form-check-input" type="checkbox" role="switch" id="donorAvailabilitySwitch" <?= !empty($userProfile['is_available_donor']) ? 'checked' : '' ?> onchange="toggleDonorAvailability(this.checked)">
                    </div>
                </div>
            </div>

            <!-- Profile Completion Progress Alert -->
            <?php if ($profileCompletion < 100): ?>
                <div class="bl-card bl-card-sm mb-4 border-primary-subtle">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-person-check-fill text-primary fs-5"></i>
                            <span class="fw-bold text-dark">Profile Completion Progress</span>
                        </div>
                        <span class="fw-extrabold text-primary"><?= $profileCompletion ?>%</span>
                    </div>
                    <div class="progress mb-2" style="height: 8px;">
                        <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?= $profileCompletion ?>%"></div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">Add your phone number, city, and bio to help requesters verify your availability.</small>
                        <a href="<?= APP_URL ?>/profile.php" class="small text-danger fw-bold text-decoration-none">Complete Profile &rarr;</a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Stat Counters Grid -->
            <div class="row g-3 mb-4">
                <div class="col-md-3 col-sm-6">
                    <div class="bl-stat-card">
                        <div class="bl-stat-icon bl-stat-icon-red">
                            <i class="bi bi-droplet-fill"></i>
                        </div>
                        <div>
                            <div class="bl-stat-value"><?= e($totalDonations) ?></div>
                            <div class="bl-stat-label">Total Donations</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="bl-stat-card">
                        <div class="bl-stat-icon bl-stat-icon-amber">
                            <i class="bi bi-heart-pulse-fill"></i>
                        </div>
                        <div>
                            <div class="bl-stat-value"><?= count($matchingRequests) ?></div>
                            <div class="bl-stat-label">Matching Requests</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="bl-stat-card">
                        <div class="bl-stat-icon bl-stat-icon-green">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <div>
                            <div class="bl-stat-value"><?= e($respondedCount) ?></div>
                            <div class="bl-stat-label">Requests Responded</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="bl-stat-card">
                        <div class="bl-stat-icon bl-stat-icon-blue">
                            <i class="bi bi-shield-heart"></i>
                        </div>
                        <div>
                            <div class="bl-stat-value"><?= e($acceptedCount * 3) ?></div>
                            <div class="bl-stat-label">Lives Supported</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Action Cards Bar -->
            <div class="row g-3 mb-4">
                <div class="col-md-3 col-6">
                    <a href="<?= APP_URL ?>/requests.php" class="bl-card bl-card-sm text-center bl-card-interactive mb-0">
                        <i class="bi bi-search text-danger fs-3 mb-2 d-block"></i>
                        <span class="fw-bold text-dark small">View Requests</span>
                    </a>
                </div>
                <div class="col-md-3 col-6">
                    <a href="<?= APP_URL ?>/profile.php" class="bl-card bl-card-sm text-center bl-card-interactive mb-0">
                        <i class="bi bi-pencil-square text-primary fs-3 mb-2 d-block"></i>
                        <span class="fw-bold text-dark small">Edit Profile</span>
                    </a>
                </div>
                <div class="col-md-3 col-6">
                    <a href="<?= APP_URL ?>/donation_history.php" class="bl-card bl-card-sm text-center bl-card-interactive mb-0">
                        <i class="bi bi-journal-text text-success fs-3 mb-2 d-block"></i>
                        <span class="fw-bold text-dark small">Donation History</span>
                    </a>
                </div>
                <div class="col-md-3 col-6">
                    <a href="<?= APP_URL ?>/notifications.php" class="bl-card bl-card-sm text-center bl-card-interactive mb-0">
                        <i class="bi bi-bell-fill text-warning fs-3 mb-2 d-block"></i>
                        <span class="fw-bold text-dark small">Notifications</span>
                    </a>
                </div>
            </div>

            <!-- Main Grid: Matching Requests & Profile Summary -->
            <div class="row g-4">
                <div class="col-lg-8">
                    <!-- Matching Blood Requests Card -->
                    <div class="bl-card mb-4">
                        <div class="bl-card-header">
                            <h3 class="bl-card-title"><i class="bi bi-heart-pulse-fill text-danger me-2"></i> Compatible Emergency Requests</h3>
                            <a href="<?= APP_URL ?>/requests.php" class="small text-danger fw-bold text-decoration-none">View All</a>
                        </div>
                        <div class="bl-card-body p-0">
                            <?php if (empty($matchingRequests)): ?>
                                <div class="p-5 text-center">
                                    <i class="bi bi-check-circle-fill text-success display-4 mb-3"></i>
                                    <h5 class="fw-bold text-dark">No Emergency Requests Pending</h5>
                                    <p class="text-muted small mb-0">There are currently no active requests matching your blood group (<?= e($userProfile['blood_group'] ?? 'O+') ?>).</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table align-middle mb-0">
                                        <thead class="table-light">
                                            <tr class="small text-uppercase text-muted">
                                                <th>Patient / Hospital</th>
                                                <th>Blood Group</th>
                                                <th>Location</th>
                                                <th>Urgency</th>
                                                <th>Date Needed</th>
                                                <th class="text-end">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($matchingRequests as $req): ?>
                                                <tr>
                                                    <td>
                                                        <div class="fw-bold text-dark"><?= e($req['patient_name']) ?></div>
                                                        <div class="small text-muted"><?= e($req['hospital_name']) ?></div>
                                                    </td>
                                                    <td>
                                                        <span class="bl-badge bl-badge-blood"><?= e($req['blood_group']) ?></span>
                                                    </td>
                                                    <td>
                                                        <span class="small text-dark"><i class="bi bi-geo-alt-fill text-danger me-1"></i><?= e($req['city']) ?></span>
                                                    </td>
                                                    <td>
                                                        <?= FormatHelper::urgencyBadge($req['urgency_level']) ?>
                                                    </td>
                                                    <td>
                                                        <span class="small text-muted"><?= FormatHelper::formatDate($req['required_date']) ?></span>
                                                    </td>
                                                    <td class="text-end">
                                                        <button type="button" class="bl-btn bl-btn-primary bl-btn-sm" onclick="openRespondModal(<?= $req['id'] ?>, '<?= e($req['patient_name']) ?>', '<?= e($req['blood_group']) ?>')">
                                                            <i class="bi bi-heart-fill"></i> Respond
                                                        </button>
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

                <!-- Donor Profile Summary & Activity -->
                <div class="col-lg-4">
                    <div class="bl-card text-center mb-4">
                        <img src="<?= APP_URL ?>/assets/images/default_avatar.png" alt="<?= e($userProfile['full_name'] ?? $currentUser['username'] ?? 'Donor') ?>" class="bl-avatar mx-auto mb-3" style="width: 80px; height: 80px;">
                        <h4 class="h5 fw-bold text-dark mb-1"><?= e($userProfile['full_name'] ?? $currentUser['username'] ?? 'Donor') ?></h4>
                        <p class="text-muted small mb-2"><i class="bi bi-geo-alt-fill text-danger me-1"></i> <?= e($userProfile['city'] ?? 'Karachi') ?></p>
                        
                        <div class="d-flex justify-content-center gap-2 mb-3">
                            <span class="bl-badge bl-badge-blood"><?= e($userProfile['blood_group'] ?? 'O+') ?></span>
                            <span class="bl-badge bl-badge-success">Voluntary Donor</span>
                        </div>

                        <div class="bg-light p-3 rounded mb-3 text-start small">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Last Donated:</span>
                                <span class="fw-bold text-dark"><?= FormatHelper::formatDate($userProfile['last_donated_at'] ?? null) ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Total Donations:</span>
                                <span class="fw-bold text-danger"><?= e($totalDonations) ?> Units</span>
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

                    <!-- Recent Activity Feed -->
                    <div class="bl-card">
                        <h4 class="h5 fw-bold text-dark mb-3"><i class="bi bi-clock-history text-primary me-2"></i> Recent Notifications</h4>
                        <?php if (empty($recentNotifications)): ?>
                            <p class="text-muted small mb-0">No recent notifications.</p>
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

<!-- Donor Respond Modal -->
<div class="modal fade bl-modal" id="respondModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-heart-fill text-danger me-2"></i> Respond to Blood Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
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
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= APP_URL ?>/assets/js/app.js"></script>

<script>
// AJAX Toggle Donor Availability Status
function toggleDonorAvailability(isAvailable) {
    const statusVal = isAvailable ? 1 : 0;
    const label = document.getElementById('availabilityStatusLabel');

    fetch('<?= APP_URL ?>/api/donor-availability.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'is_available=' + statusVal
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            label.innerHTML = isAvailable 
                ? '<span class="text-success fw-bold"><i class="bi bi-circle-fill me-1"></i> Available to Donate</span>' 
                : '<span class="text-danger fw-bold"><i class="bi bi-dash-circle-fill me-1"></i> Currently Unavailable</span>';
            showToast(data.message, 'success');
        } else {
            showToast(data.message, 'danger');
        }
    })
    .catch(err => showToast('Error updating availability.', 'danger'));
}

// Open Respond Modal
function openRespondModal(requestId, patientName, bloodGroup) {
    document.getElementById('modalRequestId').value = requestId;
    document.getElementById('modalPatientName').textContent = patientName;
    document.getElementById('modalBloodGroup').textContent = bloodGroup;

    const modal = new bootstrap.Modal(document.getElementById('respondModal'));
    modal.show();
}

// AJAX Submit Donor Response
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
</body>
</html>
