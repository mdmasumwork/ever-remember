<?php

require_once __DIR__ . '/../../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

class SecurityHeadersUtil {
    public static function setHeaders($allowedMethods) {
        $allowedOrigin = $_ENV['ALLOWED_ORIGIN'] ?: '*';
        header('Content-Type: application/json');
        header("Access-Control-Allow-Origin: $allowedOrigin");
        header("Access-Control-Allow-Methods: $allowedMethods");
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
    }

    public static function handlePreflight($allowedMethods) {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            $allowedOrigin = $_ENV['ALLOWED_ORIGIN'] ?: '*';
            header("Access-Control-Allow-Origin: $allowedOrigin");
            header("Access-Control-Allow-Methods: $allowedMethods");
            header("Access-Control-Allow-Headers: Content-Type, Authorization");
            http_response_code(200);
            exit();
        }
    }
}
