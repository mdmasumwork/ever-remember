
<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../src/services/RateLimiterService.php';

try {
    $rateLimiter = new RateLimiterService();
    echo "Redis Status: Connected";
} catch (Exception $e) {
    header("HTTP/1.1 503 Service Unavailable");
    echo "Redis Status: Failed - " . $e->getMessage();
}