<?php

class OpenAIConfig {
    private static function loadEnv() {
        $envFile = __DIR__ . '/../../.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                    list($key, $value) = explode('=', $line, 2);
                    $_ENV[trim($key)] = trim($value);
                }
            }
        }
    }

    public static function get($key) {
        self::loadEnv();
        switch ($key) {
            case 'api_key':
                return $_ENV['OPENAI_API_KEY'] ?? null;
            case 'model':
                return $_ENV['OPENAI_MODEL'] ?? 'gpt-4';
            case 'temperature':
                return $_ENV['OPENAI_TEMPERATURE'] ?? 0.7;
            default:
                return null;
        }
    }
}