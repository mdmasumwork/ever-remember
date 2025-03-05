<?php

require_once __DIR__ . '/../../src/utils/LogUtil.php';
require_once __DIR__ . '/../../src/controllers/PaymentController.php';
require_once __DIR__ . '/../../src/services/SessionService.php';
require_once __DIR__ . '/../../src/middleware/CSRFMiddleware.php';
// require_once __DIR__ . '/../../src/middleware/RateLimitMiddleware.php';
require_once __DIR__ . '/../../src/utils/SecurityHeadersUtil.php';

SecurityHeadersUtil::setHeaders('POST');
SecurityHeadersUtil::handlePreflight('POST');

$sessionService = new SessionService();
$csrf = new CSRFMiddleware();
$csrf->handle();

// $rateLimitMiddleware = new RateLimitMiddleware();
// $rateLimitMiddleware->handle('payment');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    throw new Exception('Invalid request method');
}

try {
    header('Content-Type: application/json');
    
    $controller = new PaymentController();
    $result = $controller->createPaymentIntent();
    
    echo json_encode($result);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}