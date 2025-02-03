<?php

class SessionService {
    public function getSessionStatus() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return [
            'hasExistingSession' => isset($_SESSION['version']),
            'version' => $_SESSION['version'] ?? null,
            'paymentVerified' => $_SESSION['payment_verified'] ?? false
        ];
    }

    public function clearSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = array();
        session_destroy();
        return true;
    }
}
