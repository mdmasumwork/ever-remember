<?php

require_once __DIR__ . '/../../src/includes/functions.php';
require_once __DIR__ . '/../../src/controllers/ContentController.php';
require_once __DIR__ . '/../../src/services/SessionService.php';
require_once __DIR__ . '/../../src/utils/SecurityHeadersUtil.php';
require_once __DIR__ . '/../../src/middleware/RateLimitMiddleware.php';

SecurityHeadersUtil::setHeaders('GET');
SecurityHeadersUtil::handlePreflight('GET');

$rateLimitMiddleware = new RateLimitMiddleware();
$rateLimitMiddleware->handle('content');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    throw new Exception('Invalid request method');
}

sleep(1);

$sessionService = new SessionService(); // Will initiate session in constructor

try {
    $version = isset($_GET['version']) ? (int)$_GET['version'] : null;
    
    if (!$version) {
        throw new Exception('Version parameter is required');
    }
    
    if (!isset($_SESSION['contents'][$version])) {
        throw new Exception('Content not found for this version');
    }
    
    // Use sendResponse to handle both verified and non-verified cases
    sendResponse(true, [
        'content' => $_SESSION['contents'][$version] ?? '',
        'version' => $version,
        'payment_verified' => $_SESSION['payment_verified'] ?? false
    ]);
    
} catch (Exception $e) {
    sendResponse(false, ['error' => $e->getMessage()]);
}