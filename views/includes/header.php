<?php
/**
 * BloodLife — Public Header Component
 * Centralized public navigation header using the unified design system.
 */
require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/helpers/SecurityHelper.php';
require_once __DIR__ . '/../../app/helpers/SessionHelper.php';

$isLoggedIn = SessionHelper::isLoggedIn();
$userRole = SessionHelper::getRole();
$currentUrl = $_SERVER['PHP_SELF'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' — ' . APP_NAME : APP_NAME . ' — ' . APP_TAGLINE ?></title>
    <meta name="description" content="BloodLife connects voluntary blood donors with recipients in urgent need through real-time matching and emergency request tracking.">
    
    <!-- Bootstrap 5.3 & Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- BloodLife Design System & Landing CSS -->
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/design-system.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/landing.css">
</head>
<body>

    <!-- Public Navigation Bar -->
    <nav class="bl-navbar">
        <div class="container d-flex align-items-center justify-content-between">
            <a href="<?= APP_URL ?>/index.php" class="bl-navbar-brand">
                <span class="bl-brand-icon"><i class="bi bi-droplet-fill"></i></span>
                <span>Blood<span class="text-danger">Life</span></span>
            </a>

            <div class="d-none d-md-flex align-items-center gap-1">
                <a href="<?= APP_URL ?>/index.php" class="bl-nav-link <?= strpos($currentUrl, 'index.php') !== false ? 'active' : '' ?>">Home</a>
                <a href="<?= APP_URL ?>/index.php#about" class="bl-nav-link">About</a>
                <a href="<?= APP_URL ?>/index.php#how-it-works" class="bl-nav-link">How It Works</a>
                <a href="<?= APP_URL ?>/index.php#why-us" class="bl-nav-link">Why BloodLife</a>
                <a href="<?= APP_URL ?>/donors.php" class="bl-nav-link">Find Donors</a>
                <a href="<?= APP_URL ?>/requests.php" class="bl-nav-link">Blood Requests</a>
            </div>

            <div class="d-flex align-items-center gap-2">
                <?php if ($isLoggedIn): ?>
                    <a href="<?= APP_URL ?>/dashboard.php" class="bl-btn bl-btn-primary bl-btn-sm">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                    <a href="<?= APP_URL ?>/logout.php" class="bl-btn bl-btn-light bl-btn-sm">Logout</a>
                <?php else: ?>
                    <a href="<?= APP_URL ?>/login.php" class="bl-btn bl-btn-light bl-btn-sm">Log In</a>
                    <a href="<?= APP_URL ?>/signup.php" class="bl-btn bl-btn-primary bl-btn-sm">
                        <i class="bi bi-heart-fill"></i> Sign Up
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Flash Notifications Anchor -->
    <?php require __DIR__ . '/toast.php'; ?>
