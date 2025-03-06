<?php

require_once __DIR__ . '/../../src/utils/LogUtil.php';
require_once __DIR__ . '/../../src/controllers/FeedbackController.php';
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
// $rateLimitMiddleware->handle('feedback');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    throw new Exception('Invalid request method');
}

try {
    $controller = new FeedbackController();
    $result = $controller->storeFeedback();
    echo json_encode($result);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
