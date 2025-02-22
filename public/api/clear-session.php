<?php

require_once __DIR__ . '/../../src/utils/LogUtil.php';

use Stripe\BillingPortal\Session;

require_once __DIR__ . '/../../src/services/SessionService.php';
require_once __DIR__ . '/../../src/middleware/CSRFMiddleware.php';
require_once __DIR__ . '/../../src/middleware/RateLimitMiddleware.php';
require_once __DIR__ . '/../../src/utils/SecurityHeadersUtil.php';

SecurityHeadersUtil::setHeaders('POST');
SecurityHeadersUtil::handlePreflight('POST');

$sessionService = new SessionService();
$csrf = new CSRFMiddleware();
$csrf->handle();

$rateLimitMiddleware = new RateLimitMiddleware();
$rateLimitMiddleware->handle('session');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    throw new Exception('Invalid request method');
}

try {
    SessionService::clearSession();
    
    echo json_encode([
        'success' => true,
        'message' => 'Session cleared successfully'
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to clear session: ' . $e->getMessage()
    ]);
}
