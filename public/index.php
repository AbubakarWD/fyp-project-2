<?php
/**
 * BloodLife — Public Homepage Landing Page
 * Multi-section public landing page built strictly with the BloodLife Design System.
 */
$pageTitle = "Social Blood Donation Platform";
require_once __DIR__ . '/../views/includes/header.php';
?>

<main>
    <!-- =========================================================================
         1. HERO SECTION
         ========================================================================= -->
    <section class="bl-hero">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="bl-hero-badge">
                        <i class="bi bi-heart-pulse-fill text-danger"></i> Real-Time Emergency Assistance
                    </span>
                    <h1 class="bl-hero-title">
                        Every Drop Counts. <span class="highlight">Connect & Save Lives</span> in Real Time.
                    </h1>
                    <p class="bl-hero-subtitle">
                        BloodLife bridges the gap between voluntary blood donors and patients in urgent medical need through real-time blood group compatibility matching and instant notifications.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="<?= APP_URL ?>/donors.php" class="bl-btn bl-btn-primary bl-btn-lg">
                            <i class="bi bi-search"></i> Find a Blood Donor
                        </a>
                        <a href="<?= APP_URL ?>/signup.php?role=donor" class="bl-btn bl-btn-outline bl-btn-lg">
                            <i class="bi bi-heart-fill"></i> Become a Donor
                        </a>
                    </div>

                    <!-- Trust Indicators -->
                    <div class="d-flex align-items-center gap-4 mt-4 pt-3 border-top border-light">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-shield-check text-success fs-4"></i>
                            <span class="small font-semibold text-dark">Verified Donors</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-clock-history text-primary fs-4"></i>
                            <span class="small font-semibold text-dark">24/7 Fast Search</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-lock-fill text-secondary fs-4"></i>
                            <span class="small font-semibold text-dark">Privacy Protected</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="bl-hero-visual">
                        <div class="bl-hero-circle-bg"></div>
                        <div class="bl-hero-card-floating">
                            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="bl-badge bl-badge-blood">O-</span>
                                    <span class="fw-bold text-dark fs-6">Critical Request</span>
                                </div>
                                <span class="bl-badge bl-badge-urgency-critical">
                                    <i class="bi bi-exclamation-circle-fill me-1"></i> Critical
                                </span>
                            </div>
                            <div class="mb-3">
                                <h5 class="fw-bold text-dark mb-1">Aga Khan University Hospital</h5>
                                <p class="text-muted small mb-1"><i class="bi bi-geo-alt-fill text-danger me-1"></i> Karachi, Sindh</p>
                                <p class="text-muted small mb-0"><i class="bi bi-person-fill text-primary me-1"></i> Patient: Tariq Jenkins (2 Units Needed)</p>
                            </div>
                            <div class="bg-light p-3 rounded mb-3">
                                <div class="d-flex align-items-center justify-content-between text-muted small mb-1">
                                    <span>Compatibility Match</span>
                                    <span class="fw-bold text-success">O-, O+ Compatible</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 75%"></div>
                                </div>
                            </div>
                            <a href="<?= APP_URL ?>/donors.php" class="bl-btn bl-btn-primary w-100">
                                <i class="bi bi-arrow-right-circle"></i> Respond & Help Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         2. QUICK BLOOD EMERGENCY SEARCH SECTION
         ========================================================================= -->
    <section class="bl-search-section">
        <div class="container">
            <div class="bl-search-card">
                <form action="<?= APP_URL ?>/donors.php" method="GET" class="row g-3 align-items-end">
                    <div class="col-lg-4 col-md-4">
                        <label class="bl-form-label"><i class="bi bi-droplet-fill text-danger me-1"></i> Select Blood Group</label>
                        <select name="blood_group" class="bl-form-select">
                            <option value="">All Blood Groups</option>
                            <option value="A+">A Positive (A+)</option>
                            <option value="A-">A Negative (A-)</option>
                            <option value="B+">B Positive (B+)</option>
                            <option value="B-">B Negative (B-)</option>
                            <option value="AB+">AB Positive (AB+)</option>
                            <option value="AB-">AB Negative (AB-)</option>
                            <option value="O+">O Positive (O+)</option>
                            <option value="O-">O Negative (O-)</option>
                        </select>
                    </div>

                    <div class="col-lg-5 col-md-5">
                        <label class="bl-form-label"><i class="bi bi-geo-alt-fill text-danger me-1"></i> City / Location</label>
                        <select name="city" class="bl-form-select">
                            <option value="">All Cities</option>
                            <option value="Karachi">Karachi</option>
                            <option value="Lahore">Lahore</option>
                            <option value="Islamabad">Islamabad</option>
                            <option value="Rawalpindi">Rawalpindi</option>
                            <option value="Faisalabad">Faisalabad</option>
                            <option value="Multan">Multan</option>
                            <option value="Peshawar">Peshawar</option>
                            <option value="Quetta">Quetta</option>
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-3">
                        <button type="submit" class="bl-btn bl-btn-primary w-100">
                            <i class="bi bi-search"></i> Search Donors
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         3. ABOUT BLOODLIFE SECTION
         ========================================================================= -->
    <section id="about" class="py-5 my-4">
        <div class="container py-4">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <div class="pe-lg-3">
                        <span class="bl-badge bl-badge-primary mb-3">Our Mission</span>
                        <h2 class="h1 mb-3">Closing the Critical Time Gap in Emergency Blood Shortages</h2>
                        <p class="text-muted mb-4">
                            In medical emergencies, every minute lost searching for blood donors puts lives at risk. BloodLife is designed to replace chaotic social media posts with a structured, privacy-protected social platform connecting voluntary donors directly with patients and healthcare centers.
                        </p>

                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="bl-card bl-card-sm mb-0">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bl-stat-icon bl-stat-icon-sm bl-stat-icon-red">
                                            <i class="bi bi-shield-heart"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-0">100% Free Platform</h6>
                                            <small class="text-muted">No hidden fees or charges</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="bl-card bl-card-sm mb-0">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bl-stat-icon bl-stat-icon-sm bl-stat-icon-green">
                                            <i class="bi bi-lightning-charge-fill"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-0">Instant Dispatch</h6>
                                            <small class="text-muted">Automated donor alerts</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="bl-card bl-info-card">
                        <h4 class="bl-info-card__title">
                            <i class="bi bi-info-circle-fill bl-info-card__title-icon"></i>
                            <span>Why Voluntary Donation Matters</span>
                        </h4>

                        <ul class="bl-info-list">
                            <li class="bl-info-list__item">
                                <span class="bl-info-list__icon">
                                    <i class="bi bi-check2"></i>
                                </span>

                                <div class="bl-info-list__content">
                                    <strong class="bl-info-list__heading">
                                        1 Donation Saves 3 Lives:
                                    </strong>

                                    <p class="bl-info-list__description">
                                        A single unit of whole blood can be separated into red blood cells, plasma, and platelets to help multiple patients.
                                    </p>
                                </div>
                            </li>

                            <li class="bl-info-list__item">
                                <span class="bl-info-list__icon">
                                    <i class="bi bi-check2"></i>
                                </span>

                                <div class="bl-info-list__content">
                                    <strong class="bl-info-list__heading">
                                        Universal Donor Support:
                                    </strong>

                                    <p class="bl-info-list__description">
                                        O-Negative blood can be transfused to patients of any blood type during trauma emergencies when there is no time for blood typing.
                                    </p>
                                </div>
                            </li>

                            <li class="bl-info-list__item">
                                <span class="bl-info-list__icon">
                                    <i class="bi bi-check2"></i>
                                </span>

                                <div class="bl-info-list__content">
                                    <strong class="bl-info-list__heading">
                                        Health Benefits for Donors:
                                    </strong>

                                    <p class="bl-info-list__description">
                                        Regular blood donation stimulates fresh blood cell production and helps maintain healthy iron levels.
                                    </p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         4. HOW IT WORKS SECTION
         ========================================================================= -->
    <section id="how-it-works" class="py-5 bg-white border-top border-bottom">
        <div class="container py-4">
            <div class="text-center mx-auto mb-5" style="max-width: 600px;">
                <span class="bl-badge bl-badge-primary mb-2">Step-by-Step Guide</span>
                <h2 class="h1">How BloodLife Works</h2>
                <p class="text-muted">Simple, fast, and intuitive four-step workflow to connect voluntary donors with emergency blood requests.</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="bl-step-card">
                        <div class="bl-step-number">1</div>
                        <h4 class="h5 fw-bold text-dark mb-2">Create Account</h4>
                        <p class="text-muted small mb-0">Register in under 60 seconds as a Voluntary Blood Donor or Recipient/Requester.</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="bl-step-card">
                        <div class="bl-step-number">2</div>
                        <h4 class="h5 fw-bold text-dark mb-2">Find or Request</h4>
                        <p class="text-muted small mb-0">Search registered donors by city/blood type or submit an emergency request with hospital details.</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="bl-step-card">
                        <div class="bl-step-number">3</div>
                        <h4 class="h5 fw-bold text-dark mb-2">Connect & Respond</h4>
                        <p class="text-muted small mb-0">Nearby matching donors receive alerts and respond directly through internal messaging.</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="bl-step-card">
                        <div class="bl-step-number">4</div>
                        <h4 class="h5 fw-bold text-dark mb-2">Save a Life</h4>
                        <p class="text-muted small mb-0">Coordinate donation at the hospital and log verified donation records to track your social impact.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         5. ABO BLOOD GROUPS SECTION
         ========================================================================= -->
    <section id="blood-groups" class="py-5">
        <div class="container py-4">
            <div class="text-center mx-auto mb-5" style="max-width: 600px;">
                <span class="bl-badge bl-badge-primary mb-2">ABO Compatibility</span>
                <h2 class="h1">Blood Groups & Compatibility</h2>
                <p class="text-muted">Understand blood group compatibility rules to know who you can donate to and receive from.</p>
            </div>

            <div class="row g-3">
                <!-- A+ -->
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="bl-group-card">
                        <div class="bl-group-badge-large">A+</div>
                        <h5 class="fw-bold text-dark mb-1">A Positive</h5>
                        <p class="text-muted small mb-2">Donate to: <strong>A+, AB+</strong></p>
                        <p class="text-muted small mb-0">Receive from: <strong>A+, A-, O+, O-</strong></p>
                    </div>
                </div>

                <!-- A- -->
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="bl-group-card">
                        <div class="bl-group-badge-large">A-</div>
                        <h5 class="fw-bold text-dark mb-1">A Negative</h5>
                        <p class="text-muted small mb-2">Donate to: <strong>A+, A-, AB+, AB-</strong></p>
                        <p class="text-muted small mb-0">Receive from: <strong>A-, O-</strong></p>
                    </div>
                </div>

                <!-- B+ -->
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="bl-group-card">
                        <div class="bl-group-badge-large">B+</div>
                        <h5 class="fw-bold text-dark mb-1">B Positive</h5>
                        <p class="text-muted small mb-2">Donate to: <strong>B+, AB+</strong></p>
                        <p class="text-muted small mb-0">Receive from: <strong>B+, B-, O+, O-</strong></p>
                    </div>
                </div>

                <!-- B- -->
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="bl-group-card">
                        <div class="bl-group-badge-large">B-</div>
                        <h5 class="fw-bold text-dark mb-1">B Negative</h5>
                        <p class="text-muted small mb-2">Donate to: <strong>B+, B-, AB+, AB-</strong></p>
                        <p class="text-muted small mb-0">Receive from: <strong>B-, O-</strong></p>
                    </div>
                </div>

                <!-- AB+ -->
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="bl-group-card">
                        <div class="bl-group-badge-large">AB+</div>
                        <h5 class="fw-bold text-dark mb-1">AB Positive</h5>
                        <p class="text-muted small mb-2">Donate to: <strong>AB+ Only</strong></p>
                        <p class="text-muted small mb-0">Receive from: <strong>All Blood Groups</strong></p>
                    </div>
                </div>

                <!-- AB- -->
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="bl-group-card">
                        <div class="bl-group-badge-large">AB-</div>
                        <h5 class="fw-bold text-dark mb-1">AB Negative</h5>
                        <p class="text-muted small mb-2">Donate to: <strong>AB+, AB-</strong></p>
                        <p class="text-muted small mb-0">Receive from: <strong>A-, B-, AB-, O-</strong></p>
                    </div>
                </div>

                <!-- O+ -->
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="bl-group-card">
                        <div class="bl-group-badge-large">O+</div>
                        <h5 class="fw-bold text-dark mb-1">O Positive</h5>
                        <p class="text-muted small mb-2">Donate to: <strong>O+, A+, B+, AB+</strong></p>
                        <p class="text-muted small mb-0">Receive from: <strong>O+, O-</strong></p>
                    </div>
                </div>

                <!-- O- -->
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="bl-group-card">
                        <div class="bl-group-badge-large">O-</div>
                        <h5 class="fw-bold text-dark mb-1">O Negative</h5>
                        <p class="text-muted small mb-2">Donate to: <strong>Universal Donor</strong></p>
                        <p class="text-muted small mb-0">Receive from: <strong>O- Only</strong></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         6. PLATFORM STATISTICS SECTION
         ========================================================================= -->
    <section class="py-5 bg-white border-top border-bottom">
        <div class="container py-3">
            <div class="row g-4">
                <div class="col-md-3 col-sm-6">
                    <div class="bl-stat-card">
                        <div class="bl-stat-icon bl-stat-icon-red">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div>
                            <div class="bl-stat-value">1,240+</div>
                            <div class="bl-stat-label">Registered Donors</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="bl-stat-card">
                        <div class="bl-stat-icon bl-stat-icon-amber">
                            <i class="bi bi-heart-pulse-fill"></i>
                        </div>
                        <div>
                            <div class="bl-stat-value">350+</div>
                            <div class="bl-stat-label">Blood Requests</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="bl-stat-card">
                        <div class="bl-stat-icon bl-stat-icon-green">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <div>
                            <div class="bl-stat-value">890+</div>
                            <div class="bl-stat-label">Successful Connections</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="bl-stat-card">
                        <div class="bl-stat-icon bl-stat-icon-blue">
                            <i class="bi bi-shield-heart"></i>
                        </div>
                        <div>
                            <div class="bl-stat-value">1,150+</div>
                            <div class="bl-stat-label">Lives Supported</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         7. WHY BLOODLIFE SECTION
         ========================================================================= -->
    <section id="why-us" class="py-5">
        <div class="container py-4">
            <div class="text-center mx-auto mb-5" style="max-width: 600px;">
                <span class="bl-badge bl-badge-primary mb-2">Platform Features</span>
                <h2 class="h1">Why Choose BloodLife</h2>
                <p class="text-muted">Built from the ground up for speed, security, and community-driven healthcare support.</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="bl-card h-100 mb-0">
                        <div class="bl-stat-icon bl-stat-icon-red mb-3">
                            <i class="bi bi-lightning-charge-fill"></i>
                        </div>
                        <h4 class="h5 fw-bold text-dark mb-2">Fast Donor Discovery</h4>
                        <p class="text-muted small mb-0">Locate available blood donors in your city filtered by specific blood group and active donor status instantly.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="bl-card h-100 mb-0">
                        <div class="bl-stat-icon bl-stat-icon-blue mb-3">
                            <i class="bi bi-bell-fill"></i>
                        </div>
                        <h4 class="h5 fw-bold text-dark mb-2">Automated Notifications</h4>
                        <p class="text-muted small mb-0">Whenever an emergency blood request is posted, system alerts notify matching local donors immediately.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="bl-card h-100 mb-0">
                        <div class="bl-stat-icon bl-stat-icon-green mb-3">
                            <i class="bi bi-person-lines-fill"></i>
                        </div>
                        <h4 class="h5 fw-bold text-dark mb-2">Profile-Based Control</h4>
                        <p class="text-muted small mb-0">Donors can easily toggle their availability status when eligible or on cooldown after donating.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="bl-card h-100 mb-0">
                        <div class="bl-stat-icon bl-stat-icon-amber mb-3">
                            <i class="bi bi-journal-check"></i>
                        </div>
                        <h4 class="h5 fw-bold text-dark mb-2">Donation History Tracking</h4>
                        <p class="text-muted small mb-0">Keep a verified personal log of past donations, facility locations, and units contributed.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="bl-card h-100 mb-0">
                        <div class="bl-stat-icon bl-stat-icon-red mb-3">
                            <i class="bi bi-chat-dots-fill"></i>
                        </div>
                        <h4 class="h5 fw-bold text-dark mb-2">Direct Communication</h4>
                        <p class="text-muted small mb-0">Requesters and donors can securely connect and coordinate hospital logistics in real time.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="bl-card h-100 mb-0">
                        <div class="bl-stat-icon bl-stat-icon-green mb-3">
                            <i class="bi bi-shield-lock-fill"></i>
                        </div>
                        <h4 class="h5 fw-bold text-dark mb-2">Privacy & Security</h4>
                        <p class="text-muted small mb-0">Personal phone numbers and sensitive information are protected and shared strictly with user consent.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         8. TESTIMONIALS / COMMUNITY STORIES SECTION
         ========================================================================= -->
    <section class="py-5 bg-white border-top border-bottom">
        <div class="container py-4">
            <div class="text-center mx-auto mb-5" style="max-width: 600px;">
                <span class="bl-badge bl-badge-primary mb-2">Community Stories</span>
                <h2 class="h1">Saved Lives, Real Stories</h2>
                <p class="text-muted">Hear from donors and recipients who connected through BloodLife during emergency medical crises.</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="bl-testimonial-card">
                        <div>
                            <i class="bi bi-quote bl-quote-icon"></i>
                            <p class="text-dark small mb-3">
                                "When my father was in critical surgery at Aga Khan Hospital, we urgently needed 2 units of O-Negative blood. BloodLife matched us with an O- donor within 20 minutes!"
                            </p>
                        </div>
                        <div class="d-flex align-items-center gap-3 pt-3 border-top">
                            <div class="bl-avatar bg-danger text-white d-flex align-items-center justify-content-center font-bold">SJ</div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">Sarah Jenkins</h6>
                                <small class="text-muted">Recipient / Family Member (Karachi)</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="bl-testimonial-card">
                        <div>
                            <i class="bi bi-quote bl-quote-icon"></i>
                            <p class="text-dark small mb-3">
                                "As an O-Negative universal donor, I registered on BloodLife to make sure I could respond to emergency calls. It’s incredibly rewarding to know your donation saved a life."
                            </p>
                        </div>
                        <div class="d-flex align-items-center gap-3 pt-3 border-top">
                            <div class="bl-avatar bg-primary text-white d-flex align-items-center justify-content-center font-bold">AH</div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">Ahmed Hassan</h6>
                                <small class="text-muted">Voluntary Donor (Islamabad)</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="bl-testimonial-card">
                        <div>
                            <i class="bi bi-quote bl-quote-icon"></i>
                            <p class="text-dark small mb-3">
                                "The platform is clean, fast, and simple to use. Posting a blood request took under 2 minutes, and local donors responded almost immediately."
                            </p>
                        </div>
                        <div class="d-flex align-items-center gap-3 pt-3 border-top">
                            <div class="bl-avatar bg-success text-white d-flex align-items-center justify-content-center font-bold">AR</div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">Ali Raza</h6>
                                <small class="text-muted">Requester (Faisalabad)</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         9. EMERGENCY CALL TO ACTION BANNER
         ========================================================================= -->
    <section class="py-5">
        <div class="container">
            <div class="bl-cta-banner text-center">
                <h2 class="h1 text-white mb-3">Ready to Make a Life-Saving Difference?</h2>
                <p class="text-light lead mx-auto mb-4" style="max-width: 650px;">
                    Join thousands of voluntary blood donors across the country. Registering as a donor takes under a minute and helps ensure no emergency goes unanswered.
                </p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="<?= APP_URL ?>/signup.php?role=donor" class="bl-btn bl-btn-primary bl-btn-lg">
                        <i class="bi bi-heart-fill"></i> Register as a Donor
                    </a>
                    <a href="<?= APP_URL ?>/signup.php?role=requester" class="bl-btn bl-btn-light bl-btn-lg">
                        <i class="bi bi-plus-circle-fill"></i> Create Blood Request
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/../views/includes/footer.php'; ?>
