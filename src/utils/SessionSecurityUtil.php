<?php

require_once __DIR__ . '/DebugUtil.php';

class SessionSecurityUtil {
    const SESSION_NAME = 'EVER_REMEMBER_SESSION';
    const SESSION_LIFETIME = 14400; // 4 hours
    const INACTIVITY_TIMEOUT = 1800; // 30 minutes
    const REGENERATE_INTERVAL = 120; // 15 minutes - interval to regenerate session ID
    
    public static function initiateSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_name(self::SESSION_NAME);
            session_start();
            if (!isset($_SESSION['security'])) {
                $_SESSION['security'] = [
                    'created_at' => time(),
                    'last_activity' => time()
                ];
            }
        }
    }

    public static function clearSession(): void {
        // 1. Clear session data
        $_SESSION = array();
        
        // 2. Clear session cookie
        if (isset($_COOKIE[self::SESSION_NAME])) {
            $params = session_get_cookie_params();
            setcookie(
                self::SESSION_NAME,
                '',
                [
                    'expires' => 1,  // Past time to ensure deletion
                    'path' => $params['path'],
                    'domain' => $params['domain'],
                    'secure' => $params['secure'],
                    'httponly' => $params['httponly'],
                    'samesite' => $params['samesite']
                ]
            );
            unset($_COOKIE[self::SESSION_NAME]);
        }
        
        // 3. Destroy session data on server
        if (session_id() !== '') {
            session_destroy();
        }
        
        // 4. Close session writing
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
    }

    public static function isSessionActive(): bool {
        if (session_status() !== PHP_SESSION_ACTIVE || !isset($_SESSION['security'])) {
            self::clearSession();
            return false;
        }

        $currentTime = time();
        $securityData = $_SESSION['security'];

        // Check session lifetime
        if (($currentTime - $securityData['created_at']) > self::SESSION_LIFETIME) {
            self::clearSession();
            return false;
        }

        // Check inactivity timeout
        if (($currentTime - $securityData['last_activity']) > self::INACTIVITY_TIMEOUT) {
            self::clearSession();
            return false;
        }

        // Update last activity time
        $_SESSION['security']['last_activity'] = $currentTime;
        
        // Regenerate session ID periodically for security
        if (($currentTime - $securityData['last_activity']) > self::REGENERATE_INTERVAL) {
            self::regenerateSession();
        }
        
        return true;
    }

    private static function regenerateSession(): void {
        // Store current session data
        $sessionData = $_SESSION;
        
        // Generate new session ID
        session_regenerate_id(true);
        
        // Restore session data
        $_SESSION = $sessionData;
        
        // Update security timestamps
        $_SESSION['security']['last_activity'] = time();
        
        DebugUtil::log('Session ID regenerated: ' . session_id());
    }
}
