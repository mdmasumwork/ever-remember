<?php
require_once __DIR__ . '/../../vendor/autoload.php';

class StripeConfig {
    private static function loadEnv() {
        $envFile = __DIR__ . '/../../.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    $_ENV[trim($key)] = trim($value);
                }
            }
        }
    }

    public static function get($key) {
        self::loadEnv();
        switch ($key) {
            case 'public_key':
                return $_ENV['STRIPE_PUBLIC_KEY'] ?? null;
            case 'secret_key':
                return $_ENV['STRIPE_SECRET_KEY'] ?? null;
            default:
                return null;
        }
    }
}