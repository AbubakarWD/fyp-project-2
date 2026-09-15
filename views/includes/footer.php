<?php
/**
 * BloodLife — Public Footer Component
 * Centralized public footer using the unified design system.
 */
?>
    <!-- Public Footer -->
    <footer class="bl-footer">
        <div class="container">
            <div class="row g-4 mb-4">
                <div class="col-lg-4 col-md-6">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="bl-brand-icon bl-brand-icon-sm"><i class="bi bi-droplet-fill"></i></span>
                        <span class="fs-4 fw-extrabold text-white">Blood<span class="text-danger">Life</span></span>
                    </div>
                    <p class="mb-3">
                        BloodLife is a social blood donation platform bridging voluntary donors with recipients in emergency need through fast discovery, compatible matching, and active notification tracking.
                    </p>
                    <div class="d-flex gap-2">
                        <a href="#" class="bl-social-icon" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="bl-social-icon" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
                        <a href="#" class="bl-social-icon" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="bl-social-icon" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6">
                    <h5>Quick Links</h5>
                    <a href="<?= APP_URL ?>/index.php" class="bl-footer-link">Home</a>
                    <a href="<?= APP_URL ?>/donors.php" class="bl-footer-link">Find Blood Donors</a>
                    <a href="<?= APP_URL ?>/requests.php" class="bl-footer-link">Emergency Requests</a>
                    <a href="<?= APP_URL ?>/index.php#how-it-works" class="bl-footer-link">How It Works</a>
                    <a href="<?= APP_URL ?>/index.php#why-us" class="bl-footer-link">Why Choose Us</a>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h5>Blood Groups</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="bl-badge bl-badge-blood">A+</span>
                        <span class="bl-badge bl-badge-blood">A-</span>
                        <span class="bl-badge bl-badge-blood">B+</span>
                        <span class="bl-badge bl-badge-blood">B-</span>
                        <span class="bl-badge bl-badge-blood">AB+</span>
                        <span class="bl-badge bl-badge-blood">AB-</span>
                        <span class="bl-badge bl-badge-blood">O+</span>
                        <span class="bl-badge bl-badge-blood">O-</span>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h5>Emergency Contact</h5>
                    <p class="mb-2"><i class="bi bi-geo-alt-fill text-danger me-2"></i> Healthcare Avenue, Medical City</p>
                    <p class="mb-2"><i class="bi bi-telephone-fill text-danger me-2"></i> +1 (800) 555-BLOOD</p>
                    <p class="mb-0"><i class="bi bi-envelope-fill text-danger me-2"></i> support@bloodlife.org</p>
                </div>
            </div>

            <div class="border-top border-secondary-subtle pt-4 text-center">
                <p class="mb-0">&copy; <?= date('Y') ?> BloodLife Platform. All rights reserved. Built for voluntary blood donation and emergency life-saving assistance.</p>
            </div>
        </div>
    </footer>

    <!-- JS Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="<?= APP_URL ?>/assets/js/app.js"></script>
    <script src="<?= APP_URL ?>/assets/js/validation.js"></script>
</body>
</html>
