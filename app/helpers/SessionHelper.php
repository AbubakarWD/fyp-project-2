<?php
/**
 * BloodLife — Session Helper
 * Manages user authentication sessions, role state, and flash notifications.
 */

require_once __DIR__ . '/../config/config.php';

class SessionHelper {

    /**
     * Start secure session if not already active.
     */
    public static function startSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            if (!headers_sent()) {
                $savePath = sys_get_temp_dir();
                if (@is_dir($savePath) && @is_writable($savePath)) {
                    @session_save_path($savePath);
                }
                @ini_set('session.cookie_httponly', '1');
                @ini_set('session.use_only_cookies', '1');
                @ini_set('session.gc_maxlifetime', (string)SESSION_LIFETIME);
            }
            @session_start();
        }
    }

    /**
     * Log in a user and store session state.
     *
     * @param array $user Data array containing user id, email, username, role, full_name, etc.
     */
    public static function login(array $user): void {
        self::startSession();
        if (session_status() === PHP_SESSION_ACTIVE && !headers_sent()) {
            session_regenerate_id(true);
        }

        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['username'] = $user['username'] ?? '';
        $_SESSION['role'] = $user['primary_role'] ?? 'donor';
        $_SESSION['full_name'] = $user['full_name'] ?? $user['username'];
        $_SESSION['blood_group'] = $user['blood_group'] ?? '';
        $_SESSION['avatar'] = $user['avatar'] ?? 'default_avatar.png';
        $_SESSION['logged_in_at'] = time();
    }

    /**
     * Log out user and destroy session completely.
     */
    public static function logout(): void {
        self::startSession();
        $_SESSION = array();

        if (ini_get("session.use_cookies") && !headers_sent()) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            @session_destroy();
        }
    }

    /**
     * Check if a user is currently logged in.
     *
     * @return bool
     */
    public static function isLoggedIn(): bool {
        self::startSession();
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    /**
     * Get currently logged-in user ID or null.
     *
     * @return int|null
     */
    public static function getUserId(): ?int {
        self::startSession();
        return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
    }

    /**
     * Get currently logged in user role ('donor' or 'requester').
     *
     * @return string|null
     */
    public static function getRole(): ?string {
        self::startSession();
        return $_SESSION['role'] ?? null;
    }

    /**
     * Check if user is a donor.
     *
     * @return bool
     */
    public static function isDonor(): bool {
        return self::getRole() === 'donor';
    }

    /**
     * Check if user is a requester.
     *
     * @return bool
     */
    public static function isRequester(): bool {
        return self::getRole() === 'requester';
    }

    /**
     * Set a flash message for next page render.
     *
     * @param string $type Message type ('success', 'danger', 'warning', 'info')
     * @param string $message Text content
     */
    public static function setFlash(string $type, string $message): void {
        self::startSession();
        $_SESSION['flash'] = [
            'type'    => $type,
            'message' => $message
        ];
    }

    /**
     * Retrieve and clear flash message.
     *
     * @return array|null
     */
    public static function getFlash(): ?array {
        self::startSession();
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }
}
