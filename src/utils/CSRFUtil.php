<?php

class CSRFUtil {
    public static function generateToken(): string {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            SessionSecurityUtil::initiateSession();
        }
        
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function verifyToken(?string $token): bool {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return false;
        }
        return (!empty($_SESSION['csrf_token']) && 
                !empty($token) && 
                hash_equals($_SESSION['csrf_token'], $token));
    }
}
