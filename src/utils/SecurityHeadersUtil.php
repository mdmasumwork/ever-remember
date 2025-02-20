<?php

require_once __DIR__ . '/../../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

class SecurityHeadersUtil {
    private static function getCSPDirectives() {
        return [
            "default-src 'self'",
            "script-src 'self'",
            "style-src 'self'",
            "img-src 'self'",
            "font-src 'self'",
            "frame-src 'none'",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'"
        ];
    }

    private static function getPermissionsPolicyDirectives() {
        return [
            "accelerometer=()",
            "camera=()",
            "geolocation=()",
            "gyroscope=()",
            "magnetometer=()",
            "microphone=()",
            "payment=()",
            "usb=()"
        ];
    }

    private static function getIndexCSPDirectives() {
        return [
            "default-src 'self'",
            "script-src 'self' https://code.jquery.com https://js.stripe.com 'unsafe-inline'",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
            "font-src 'self' https://fonts.gstatic.com",
            "img-src 'self'",
            "frame-src 'self' https://js.stripe.com",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'"
        ];
    }

    public static function setHeaders($allowedMethods, $contentType = 'application/json') {
        $allowedOrigin = $_ENV['ALLOWED_ORIGIN'] ?: '*';
        header("Content-Type: $contentType");
        header("Access-Control-Allow-Origin: $allowedOrigin");
        header("Access-Control-Allow-Methods: $allowedMethods");
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        
        // Add CSP header
        $cspDirectives = implode('; ', self::getCSPDirectives());
        header("Content-Security-Policy: " . $cspDirectives);
        
        // Add additional security headers
        header("X-Content-Type-Options: nosniff");
        header("X-Frame-Options: DENY");
        header("X-XSS-Protection: 1; mode=block");
        header("Referrer-Policy: strict-origin-when-cross-origin");
        
        // Add Permissions-Policy header
        $permissionsPolicy = implode(', ', self::getPermissionsPolicyDirectives());
        header("Permissions-Policy: " . $permissionsPolicy);
        
        // Add Cross-Origin isolation headers
        header("Cross-Origin-Opener-Policy: same-origin");
        header("Cross-Origin-Embedder-Policy: require-corp");
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

    public static function setIndexHeaders($allowedMethods, $contentType = 'text/html') {
        header("Content-Type: $contentType");
        $allowedOrigin = $_ENV['ALLOWED_ORIGIN'] ?: '*';
        header("Access-Control-Allow-Origin: $allowedOrigin");
        header("Access-Control-Allow-Methods: $allowedMethods");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        // Add CSP header
        $cspDirectives = implode('; ', self::getIndexCSPDirectives());
        header("Content-Security-Policy: " . $cspDirectives);
        // Add additional security headers
        header("X-Content-Type-Options: nosniff");
        header("X-Frame-Options: DENY");
        header("X-XSS-Protection: 1; mode=block");
        header("Referrer-Policy: strict-origin-when-cross-origin");
        // Add Permissions-Policy header
        $permissionsPolicy = implode(', ', self::getPermissionsPolicyDirectives());
        header("Permissions-Policy: " . $permissionsPolicy);
        // Remove Cross-Origin isolation headers
        header_remove("Cross-Origin-Opener-Policy");
        header_remove("Cross-Origin-Embedder-Policy");
    }
}
