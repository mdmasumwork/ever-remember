<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;

class EnvUtil {
    public static function loadEnvFile() {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();
    }

    public static function getEnv($key, $default = null) {
        return $_ENV[$key] ?? $default;
    }

    public static function isProduction() {
        return self::getEnv('APP_ENV', 'development') === 'production';
    }
    
    public static function isDevelopment() {
        return !self::isProduction();
    }
}

// Load environment variables
EnvUtil::loadEnvFile();
