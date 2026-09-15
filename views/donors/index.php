<?php
/**
 * BloodLife — Donor Discovery & Search View
 */
require_once ROOT_PATH . '/views/includes/header.php';
?>

<main class="py-5">
    <div class="container">
        <!-- Search & Filter Header -->
        <div class="bl-card bl-card-lg mb-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4 pb-3 border-bottom">
                <div>
                    <h1 class="h2 text-dark mb-1"><i class="bi bi-search text-danger me-2"></i> Find Voluntary Blood Donors</h1>
                    <p class="text-muted small mb-0">Search registered donors filtered by ABO blood type, city location, and immediate availability status.</p>
                </div>
                <span class="bl-badge bl-badge-primary p-2 fs-6">
                    <i class="bi bi-people-fill me-1"></i> <?= e($totalDonors) ?> Donors Available
                </span>
            </div>

            <!-- Filter Controls Form -->
            <form action="<?= APP_URL ?>/donors.php" method="GET" class="row g-3">
                <div class="col-lg-3 col-md-6">
                    <label class="bl-form-label">Search Name / City</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="bl-form-control border-start-0" placeholder="Donor name or area..." value="<?= e($search) ?>">
                    </div>
                </div>

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
                    <label class="bl-form-label">Availability Status</label>
                    <select name="is_available_donor" class="bl-form-select">
                        <option value="">All Donors</option>
                        <option value="1" <?= $isAvailable === '1' ? 'selected' : '' ?>>Available to Donate Only</option>
                        <option value="0" <?= $isAvailable === '0' ? 'selected' : '' ?>>Currently Unavailable</option>
                    </select>
                </div>

                <div class="col-12 d-flex justify-content-end gap-2 pt-2">
                    <a href="<?= APP_URL ?>/donors.php" class="bl-btn bl-btn-light">Reset Filters</a>
                    <button type="submit" class="bl-btn bl-btn-primary">
                        <i class="bi bi-funnel-fill"></i> Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- Donors Grid -->
        <?php if (empty($donors)): ?>
            <div class="bl-card bl-card-lg text-center">
                <i class="bi bi-search text-muted display-3 mb-3"></i>
                <h3 class="h4 text-dark mb-2">No Matching Donors Found</h3>
                <p class="text-muted small mb-4">Try broadening your search filters or select a neighboring city.</p>
                <a href="<?= APP_URL ?>/donors.php" class="bl-btn bl-btn-primary">Clear All Filters</a>
            </div>
        <?php else: ?>
            <div class="row g-4 mb-4">
                <?php foreach ($donors as $donor): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="bl-card h-100 text-center mb-0">
                            <div class="position-relative d-inline-block mx-auto mb-3">
                                <img src="<?= APP_URL ?>/assets/images/default_avatar.png" alt="<?= e($donor['full_name']) ?>" class="bl-avatar" style="width: 76px; height: 76px;">
                                <?php if (!empty($donor['is_available_donor'])): ?>
                                    <span class="position-absolute bottom-0 end-0 p-2 bg-success border border-light rounded-circle" title="Available to Donate"></span>
                                <?php else: ?>
                                    <span class="position-absolute bottom-0 end-0 p-2 bg-secondary border border-light rounded-circle" title="Unavailable"></span>
                                <?php endif; ?>
                            </div>

                            <h4 class="h5 fw-bold text-dark mb-1"><?= e($donor['full_name']) ?></h4>
                            <p class="text-muted small mb-2"><i class="bi bi-geo-alt-fill text-danger me-1"></i> <?= e($donor['city']) ?></p>

                            <div class="d-flex justify-content-center align-items-center gap-2 mb-3">
                                <span class="bl-badge bl-badge-blood"><?= e($donor['blood_group']) ?></span>
                                <?php if (!empty($donor['is_available_donor'])): ?>
                                    <span class="bl-badge bl-badge-success">Available</span>
                                <?php else: ?>
                                    <span class="bl-badge bl-badge-secondary">Unavailable</span>
                                <?php endif; ?>
                            </div>

                            <div class="bg-light p-3 rounded mb-3 text-start small">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Total Donations:</span>
                                    <span class="fw-bold text-dark"><?= e($donor['total_donations_count']) ?> Verified</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Last Donated:</span>
                                    <span class="fw-bold text-secondary"><?= FormatHelper::formatDate($donor['last_donated_at']) ?></span>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="button" class="bl-btn bl-btn-outline w-100 bl-btn-sm" 
                                        onclick="openDonorProfileModal(<?= htmlspecialchars(json_encode([
                                            'full_name' => $donor['full_name'],
                                            'blood_group' => $donor['blood_group'],
                                            'city' => $donor['city'],
                                            'phone_number' => $donor['phone_number'],
                                            'bio' => $donor['bio'] ?? 'Voluntary registered donor.',
                                            'total_donations_count' => $donor['total_donations_count'],
                                            'last_donated_at' => FormatHelper::formatDate($donor['last_donated_at']),
                                            'is_available' => !empty($donor['is_available_donor'])
                                        ]), ENT_QUOTES, 'UTF-8') ?>)">
                                    <i class="bi bi-person-lines-fill"></i> View Profile
                                </button>
                                <?php if (SessionHelper::isLoggedIn()): ?>
                                    <a href="<?= APP_URL ?>/request-create.php" class="bl-btn bl-btn-primary bl-btn-sm flex-shrink-0" title="Request Blood">
                                        <i class="bi bi-heart-pulse-fill"></i> Request
                                    </a>
                                <?php else: ?>
                                    <a href="<?= APP_URL ?>/login.php" class="bl-btn bl-btn-primary bl-btn-sm flex-shrink-0" title="Log in to connect">
                                        <i class="bi bi-box-arrow-in-right"></i> Connect
                                    </a>
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
                            <a class="page-link" href="<?= APP_URL ?>/donors.php?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&city=<?= urlencode($city) ?>&blood_group_id=<?= $bloodGroupId ?>">Previous</a>
                        </li>
                        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                            <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                                <a class="page-link" href="<?= APP_URL ?>/donors.php?page=<?= $p ?>&search=<?= urlencode($search) ?>&city=<?= urlencode($city) ?>&blood_group_id=<?= $bloodGroupId ?>"><?= $p ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= APP_URL ?>/donors.php?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&city=<?= urlencode($city) ?>&blood_group_id=<?= $bloodGroupId ?>">Next</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</main>

<!-- Donor Profile Modal -->
<div class="modal fade bl-modal" id="donorProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 pb-4">
                <img src="<?= APP_URL ?>/assets/images/default_avatar.png" alt="Donor" class="bl-avatar mx-auto mb-3" style="width: 84px; height: 84px;">
                <h4 class="h5 fw-bold text-dark mb-1" id="modalDonorName"></h4>
                <p class="text-muted small mb-2"><i class="bi bi-geo-alt-fill text-danger me-1"></i> <span id="modalDonorCity"></span></p>

                <div class="d-flex justify-content-center gap-2 mb-3">
                    <span class="bl-badge bl-badge-blood" id="modalDonorBloodGroup"></span>
                    <span id="modalDonorStatus"></span>
                </div>

                <p class="text-muted small mb-4" id="modalDonorBio"></p>

                <div class="bg-light p-3 rounded mb-4 text-start small">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Donations:</span>
                        <strong id="modalDonorTotal"></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Last Donation Date:</span>
                        <strong id="modalDonorLastDonated"></strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Contact Phone:</span>
                        <strong class="text-danger" id="modalDonorPhone"></strong>
                    </div>
                </div>

                <?php if (SessionHelper::isLoggedIn()): ?>
                    <a href="<?= APP_URL ?>/request-create.php" class="bl-btn bl-btn-primary w-100">
                        <i class="bi bi-heart-pulse-fill"></i> Submit Request to This Donor
                    </a>
                <?php else: ?>
                    <a href="<?= APP_URL ?>/login.php" class="bl-btn bl-btn-primary w-100">
                        <i class="bi bi-box-arrow-in-right"></i> Log In to Contact Donor
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function openDonorProfileModal(donor) {
    document.getElementById('modalDonorName').textContent = donor.full_name;
    document.getElementById('modalDonorCity').textContent = donor.city;
    document.getElementById('modalDonorBloodGroup').textContent = donor.blood_group;
    document.getElementById('modalDonorBio').textContent = donor.bio;
    document.getElementById('modalDonorTotal').textContent = donor.total_donations_count + ' Verified';
    document.getElementById('modalDonorLastDonated').textContent = donor.last_donated_at;
    
    // Privacy Protected Phone Handling for Guests vs Logged-in
    <?php if (SessionHelper::isLoggedIn()): ?>
        document.getElementById('modalDonorPhone').textContent = donor.phone_number;
    <?php else: ?>
        document.getElementById('modalDonorPhone').textContent = donor.phone_number.substring(0, 6) + '***** (Log in to view)';
    <?php endif; ?>

    const statusSpan = document.getElementById('modalDonorStatus');
    if (donor.is_available) {
        statusSpan.className = 'bl-badge bl-badge-success';
        statusSpan.textContent = 'Available to Donate';
    } else {
        statusSpan.className = 'bl-badge bl-badge-secondary';
        statusSpan.textContent = 'Currently Unavailable';
    }

    const modal = new bootstrap.Modal(document.getElementById('donorProfileModal'));
    modal.show();
}
</script>

<?php require_once ROOT_PATH . '/views/includes/footer.php'; ?>
