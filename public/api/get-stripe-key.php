<?php

require_once __DIR__ . '/../../src/utils/LogUtil.php';
require_once __DIR__ . '/../../src/config/stripe.php';
require_once __DIR__ . '/../../src/utils/SecurityHeadersUtil.php';
require_once __DIR__ . '/../../src/middleware/RateLimitMiddleware.php';

SecurityHeadersUtil::setHeaders('GET');
SecurityHeadersUtil::handlePreflight('GET');

$rateLimitMiddleware = new RateLimitMiddleware();
$rateLimitMiddleware->handle('stripe');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    throw new Exception('Invalid request method');
}

header('Content-Type: application/json');
echo json_encode(['publicKey' => StripeConfig::get('public_key')]);