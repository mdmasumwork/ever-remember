<?php

require_once __DIR__ . '/../../src/utils/LogUtil.php';
require_once __DIR__ . '/../../src/controllers/ContentController.php';
require_once __DIR__ . '/../../src/services/SessionService.php';
require_once __DIR__ . '/../../src/middleware/CSRFMiddleware.php';
// require_once __DIR__ . '/../../src/middleware/RateLimitMiddleware.php';
require_once __DIR__ . '/../../src/utils/SecurityHeadersUtil.php';

SecurityHeadersUtil::setHeaders('POST');
SecurityHeadersUtil::handlePreflight('POST');

$sessionService = new SessionService(); // Will initiate session in constructor
$sessionService->isSessionActive();

$csrf = new CSRFMiddleware();
$csrf->handle();

// $rateLimitMiddleware = new RateLimitMiddleware();
// $rateLimitMiddleware->handle('content');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    throw new Exception('Invalid request method');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $controller = new ContentController();
        $controller->handleRequest();
    } catch (Exception $e) {
        LogUtil::log('error', 'generate-content.php: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'error' => true,
            'message' => $e->getMessage()
        ]);
    }
}