<?php

class CSRFUtil {
    
    public static function generateToken(string $page = ''): string {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            SessionSecurityUtil::initiateSession();
        }
        
        $token = bin2hex(random_bytes(32));
        
        if (!empty($page)) {
            $_SESSION['csrf_token_' . $page] = $token;
            return $token;
        }
        
        // If it is not for any specific page, store as the main application token
        $_SESSION['csrf_token'] = $token;
        return $_SESSION['csrf_token'];
    }
    

    public static function verifyToken(?string $token, string $page = ''): bool {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return false;
        }
        
        // Check page-specific token if specified
        if (!empty($page)) {
            $sessionToken = $_SESSION['csrf_token_' . $page] ?? '';
            if (!empty($sessionToken) && !empty($token) && hash_equals($sessionToken, $token)) {
                return true;
            }
        }
        
        // Fall back to main token
        return (!empty($_SESSION['csrf_token']) && 
                !empty($token) && 
                hash_equals($_SESSION['csrf_token'], $token));
    }
}
