<?php
/**
 * BloodLife — Security Helper
 * Handles XSS escaping, CSRF protection, input sanitization, and password hashing.
 */

class SecurityHelper {

    /**
     * Escape HTML output to prevent XSS attacks.
     *
     * @param string|null $value
     * @return string
     */
    public static function e(?string $value): string {
        if ($value === null) {
            return '';
        }
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Generate a cryptographically secure CSRF token and store it in session.
     *
     * @return string
     */
    public static function generateCsrfToken(): string {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }
        if (empty($_SESSION[CSRF_TOKEN_KEY])) {
            $_SESSION[CSRF_TOKEN_KEY] = bin2hex(random_bytes(32));
        }
        return $_SESSION[CSRF_TOKEN_KEY];
    }

    /**
     * Render hidden CSRF token input tag for HTML forms.
     *
     * @return string
     */
    public static function csrfInput(): string {
        $token = self::generateCsrfToken();
        return '<input type="hidden" name="csrf_token" value="' . self::e($token) . '">';
    }

    public static function getCsrfTokenInput(): string {
        return self::csrfInput();
    }

    /**
     * Verify CSRF token submitted from POST or HTTP header.
     *
     * @param string|null $token
     * @return bool
     */
    public static function verifyCsrfToken(?string $token): bool {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }
        if (empty($token) || empty($_SESSION[CSRF_TOKEN_KEY])) {
            return false;
        }
        return hash_equals($_SESSION[CSRF_TOKEN_KEY], $token);
    }

    public static function validateCsrfToken(?string $token): bool {
        return self::verifyCsrfToken($token);
    }

    public static function sanitizeOutput(?string $value): string {
        return self::e($value);
    }

    /**
     * Hash user password securely using BCRYPT.
     *
     * @param string $password
     * @return string
     */
    public static function hashPassword(string $password): string {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Verify plain password against stored hash.
     *
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public static function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }

    /**
     * Sanitize string input.
     *
     * @param string $input
     * @return string
     */
    public static function sanitizeString(string $input): string {
        return trim(strip_tags($input));
    }
}

/**
 * Global shorthand helper function for HTML output escaping.
 *
 * @param string|null $value
 * @return string
 */
function e(?string $value): string {
    return SecurityHelper::e($value);
}
