<?php
require_once __DIR__ . '/../utils/EnvUtil.php';

class StripeConfig {
    public static function get($key) {
        switch ($key) {
            case 'public_key':
                if (EnvUtil::isProduction()) {
                    return EnvUtil::getEnv('STRIPE_PUBLIC_KEY_LIVE');
                } else {
                    return EnvUtil::getEnv('STRIPE_PUBLIC_KEY_TEST');
                }
            case 'secret_key':
                if (EnvUtil::isProduction()) {
                    return EnvUtil::getEnv('STRIPE_SECRET_KEY_LIVE');
                } else {
                    return EnvUtil::getEnv('STRIPE_SECRET_KEY_TEST');
                }
            default:
                return null;
        }
    }
}