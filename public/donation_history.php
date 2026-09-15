<?php
/**
 * BloodLife — Donation History Route Entry Point
 */

require_once __DIR__ . '/../app/controllers/DonationController.php';

$action = $_GET['action'] ?? 'index';

if ($action === 'store') {
    DonationController::store();
} else {
    DonationController::index();
}
