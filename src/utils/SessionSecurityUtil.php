<?php

class SessionSecurityUtil {
    const SESSION_NAME = 'EVER_REMEMBER_SESSION';
    const SESSION_LIFETIME = 120; // 4 hours
    const INACTIVITY_TIMEOUT = 3600; // 30 minutes
    const REGENERATE_INTERVAL = 3600; // 15 minutes - interval to regenerate session ID
    
    public static function initiateSession(): void {
        if (session_status() === PHP_SESSION_NONE) {            
            // Set session security settings
            $isSecure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
            ini_set('session.cookie_secure', $isSecure ? 1 : 0);
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_only_cookies', 1);

            session_name(self::SESSION_NAME);
            session_set_cookie_params([
                'lifetime' => self::SESSION_LIFETIME,
                'path' => '/',
                'domain' => $_SERVER['HTTP_HOST'],
                'secure' => $isSecure,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            
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
        LogUtil::log('content', '[SessionID: ' . session_id() . '][Session]: Clearing session...');
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

        LogUtil::log('content', '[SessionID: ' . session_id() . '][Session]: Cleared');
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
        $oldSessionId = session_id();
        // Store current session data
        $sessionData = $_SESSION;
        
        // Generate new session ID
        session_regenerate_id(true);
        
        // Restore session data
        $_SESSION = $sessionData;
        
        // Update security timestamps
        $_SESSION['security']['last_activity'] = time();
        LogUtil::log('content', '[SessionID: ' . session_id() . '][Session]: Regenerated session ID from ' . $oldSessionId . ' to ' . session_id());
    }
}
