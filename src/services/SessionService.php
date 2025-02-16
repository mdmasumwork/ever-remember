<?php

use Stripe\BillingPortal\Session;

require_once __DIR__ . '/../utils/SessionSecurityUtil.php';

class SessionService {

    public function __construct() {
        SessionSecurityUtil::initiateSession();
    }

    /**
     * Get current session status without starting a new session
     */
    public function getSessionStatus(): array {
        if (SessionSecurityUtil::isSessionActive()) {
            return [
                'hasExistingSession' => isset($_SESSION['security']),
                'version' => $_SESSION['version'] ?? null,
                'paymentVerified' => $_SESSION['payment_verified'] ?? false,
                'isExpired' => false
            ];
        } else {
            return [
                'hasExistingSession' => false,
                'version' => null,
                'paymentVerified' => false,
                'isExpired' => true
            ];
        }        
    }

    /**
     * Create or resume a session
     */
    public static function createSession(): void {
        SessionSecurityUtil::initiateSession();
    }

    /**
     * Clear and destroy the current session
     */
    public static function clearSession(): void {
        SessionSecurityUtil::clearSession();
    }
}
