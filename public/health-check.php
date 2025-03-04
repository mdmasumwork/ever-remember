<?php

require_once __DIR__ . '/../src/utils/EnvUtil.php';
require_once __DIR__ . '/../src/services/RateLimiterService.php';

// Check if we're in production - if so, return 404
if (EnvUtil::isProduction()) {
    header("HTTP/1.0 404 Not Found");
    exit("404 Not Found");
}

try {
    $rateLimiter = new RateLimiterService();
    echo "Redis Status: Connected";
} catch (Exception $e) {
    header("HTTP/1.1 503 Service Unavailable");
    echo "Redis Status: Failed - " . $e->getMessage();
}