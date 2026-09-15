<?php
/**
 * BloodLife — Auth Middleware
 * Enforces session authentication guards and role-based access control.
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/SessionHelper.php';

class AuthMiddleware {

    /**
     * Require logged-in session. Redirects to login.php if guest.
     */
    public static function requireAuth(): void {
        if (!SessionHelper::isLoggedIn()) {
            SessionHelper::setFlash('warning', 'Please log in to access your dashboard and account features.');
            header("Location: " . APP_URL . "/login.php");
            exit;
        }
    }

    /**
     * Require guest state. Redirects logged-in users to dashboard.php.
     */
    public static function requireGuest(): void {
        if (SessionHelper::isLoggedIn()) {
            header("Location: " . APP_URL . "/dashboard.php");
            exit;
        }
    }

    /**
     * Require specific role ('donor' or 'requester').
     *
     * @param string $requiredRole
     */
    public static function requireRole(string $requiredRole): void {
        self::requireAuth();
        $userRole = SessionHelper::getRole();

        if ($userRole !== $requiredRole && $userRole !== 'both') {
            SessionHelper::setFlash('danger', 'You do not have access to this role-specific area.');
            header("Location: " . APP_URL . "/dashboard.php");
            exit;
        }
    }
}
