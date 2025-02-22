<?php

class EnvUtil {
    private static function getEnv($key, $default = null) {
        return getenv($key) ?: $default;
    }

    public static function isProduction() {
        return self::getEnv('APP_ENV', 'development') === 'production';
    }
    
    public static function isDevelopment() {
        return !self::isProduction();
    }

    public static function loadEnvFile() {
        $envFile = __DIR__ . '/../../.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                    list($key, $value) = explode('=', $line, 2);
                    putenv(trim($key) . '=' . trim($value));
                }
            }
        } else {
            LogUtil::log('error', 'EnvUtil:loadEnvFile(): No .env file found.');
        }
    }
}
