<?php

require_once __DIR__ . '/EnvUtil.php';
require_once __DIR__ . '/LogUtil.php';

class SessionSecurityUtil {
    private static $sessionName;
    private static $sessionLifetime;
    private static $inactivityTimeout;
    private static $regenerateInterval;

    public static function initialize() {
        self::$sessionName = EnvUtil::getEnv('SESSION_NAME', 'EVER_REMEMBER_SESSION');
        self::$sessionLifetime = EnvUtil::getEnv('SESSION_LIFETIME', 14400); // 4 hours
        self::$inactivityTimeout = EnvUtil::getEnv('INACTIVITY_TIMEOUT', 1800); // 30 minutes
        self::$regenerateInterval = EnvUtil::getEnv('REGENERATE_INTERVAL', 900); // 15 minutes
    }

    public static function initiateSession(): void {
        if (session_status() === PHP_SESSION_NONE) {            
            // Set session security settings
            $isSecure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
            ini_set('session.cookie_secure', $isSecure ? 1 : 0);
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_only_cookies', 1);

            session_name(self::$sessionName);
            session_set_cookie_params([
                'lifetime' => self::$sessionLifetime,
                'path' => '/',
                'domain' => $_SERVER['HTTP_HOST'],
                'secure' => $isSecure,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            
            session_start();
            if (!isset($_SESSION['security'])) {
                LogUtil::log('content', '[SessionID: ' . session_id() . '][Session]: Session created...');
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
        if (isset($_COOKIE[self::$sessionName])) {
            $params = session_get_cookie_params();
            setcookie(
                self::$sessionName,
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
            unset($_COOKIE[self::$sessionName]);
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
        if (($currentTime - $securityData['created_at']) > self::$sessionLifetime) {
            LogUtil::log('content', '[SessionID: ' . session_id() . '][Session]: Lifespan exceeded');
            self::clearSession();
            return false;
        }

        // Check inactivity timeout
        if (($currentTime - $securityData['last_activity']) > self::$inactivityTimeout) {
            LogUtil::log('content', '[SessionID: ' . session_id() . '][Session]: Inactivity timeout exceeded');
            self::clearSession();
            return false;
        }

        // Update last activity time
        $_SESSION['security']['last_activity'] = $currentTime;
        
        // Regenerate session ID periodically for security
        if (($currentTime - $securityData['created_at']) > self::$regenerateInterval) {
            LogUtil::log('content', '[SessionID: ' . session_id() . '][Session]: Regenarete time exceeded');
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
        $_SESSION['security']['created_at'] = time();
        LogUtil::log('content', '[SessionID: ' . session_id() . '][Session]: Regenerated session ID from ' . $oldSessionId . ' to ' . session_id());
    }
}

// Initialize session settings
SessionSecurityUtil::initialize();
