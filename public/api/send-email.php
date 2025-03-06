<?php

require_once __DIR__ . '/../../src/utils/LogUtil.php';
require_once __DIR__ . '/../../src/controllers/EmailController.php';
require_once __DIR__ . '/../../src/services/SessionService.php';
require_once __DIR__ . '/../../src/middleware/CSRFMiddleware.php';
// require_once __DIR__ . '/../../src/middleware/RateLimitMiddleware.php';
require_once __DIR__ . '/../../src/utils/SecurityHeadersUtil.php';

SecurityHeadersUtil::setHeaders('POST');
SecurityHeadersUtil::handlePreflight('POST');

$sessionService = new SessionService();
$sessionService->isSessionActive();

$csrf = new CSRFMiddleware();
$csrf->handle();

// $rateLimitMiddleware = new RateLimitMiddleware();
// $rateLimitMiddleware->handle('email');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    throw new Exception('Invalid request method');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Parse JSON if present
    if (strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
        $raw = file_get_contents('php://input');
        $_POST = json_decode($raw, true) ?? [];
    }

    try {
        $controller = new EmailController();
        $controller->handleRequest();
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'error' => true,
            'message' => $e->getMessage()
        ]);
    }
}
