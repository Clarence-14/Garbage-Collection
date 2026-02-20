<?php
namespace Src\Security;

class CSRF {
    /**
     * Generate and store a CSRF token in the session if one doesn't exist.
     * @return string The CSRF token.
     */
    public static function getToken() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }

    /**
     * Generate an HTML hidden input field containing the CSRF token.
     * @return string
     */
    public static function getField() {
        $token = self::getToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }

    /**
     * Verify the provided CSRF token against the one stored in the session.
     * @param string $token The token to verify.
     * @return bool True if valid, false otherwise.
     */
    public static function verifyToken($token) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }
}
