<?php

use Stripe\BillingPortal\Session;

require_once __DIR__ . '/SessionSecurityUtil.php';

class AuthUtil {
    public static function hasAccessToTheContent(): bool {
        
        SessionSecurityUtil::initiateSession();

        if ((isset($_SESSION['payment_verified']) && $_SESSION['payment_verified']) || (isset($_SESSION['got_full_discount']) && $_SESSION['got_full_discount'])) {
            return true;
        } else {
            return false;
        }
    }
}