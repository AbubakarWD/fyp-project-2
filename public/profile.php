<?php
/**
 * BloodLife — Profile Route Entry Point
 */

require_once __DIR__ . '/../app/controllers/ProfileController.php';

$action = $_GET['action'] ?? 'index';

switch ($action) {
    case 'update':
        ProfileController::update();
        break;
    case 'avatar':
        ProfileController::uploadAvatar();
        break;
    default:
        ProfileController::index();
        break;
}
