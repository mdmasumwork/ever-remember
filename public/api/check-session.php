<?php

require_once __DIR__ . '/../../src/utils/LogUtil.php';
require_once __DIR__ . '/../../src/services/SessionService.php';
require_once __DIR__ . '/../../src/utils/SecurityHeadersUtil.php';
// require_once __DIR__ . '/../../src/middleware/RateLimitMiddleware.php';

SecurityHeadersUtil::setHeaders('GET');
SecurityHeadersUtil::handlePreflight('GET');

// $rateLimitMiddleware = new RateLimitMiddleware();
// $rateLimitMiddleware->handle('session');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    throw new Exception('Invalid request method');
}

try {
    $sessionService = new SessionService();
    $status = $sessionService->getSessionStatus();

    echo json_encode([
        'success' => true,
        'data' => $status
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
