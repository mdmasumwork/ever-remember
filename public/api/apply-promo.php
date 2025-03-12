<?php

require_once __DIR__ . '/../../src/utils/LogUtil.php';
require_once __DIR__ . '/../../src/includes/functions.php';
require_once __DIR__ . '/../../src/services/SessionService.php';
require_once __DIR__ . '/../../src/utils/SecurityHeadersUtil.php';
require_once __DIR__ . '/../../src/middleware/CSRFMiddleware.php';
require_once __DIR__ . '/../../src/utils/EnvUtil.php';
require_once __DIR__ . '/../../src/config/Database.php';


sleep(2);


// 1. Add initial security like store-form-data.php
SecurityHeadersUtil::setHeaders('POST');
SecurityHeadersUtil::handlePreflight('POST');

$sessionService = new SessionService(); // Will initiate session in constructor
$sessionService->isSessionActive();

$csrf = new CSRFMiddleware();
$csrf->handle();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

try {
    $postData = json_decode(file_get_contents('php://input'), true);
    
    if (!$postData || !isset($postData['promoCode'])) {
        throw new Exception('Missing required parameters');
    }
    
    $promoCode = trim($postData['promoCode']);
    $isRemoval = isset($postData['remove']) && $postData['remove'] === true;
    
    if (empty($promoCode)) {
        throw new Exception('Promo code cannot be empty');
    }
    
    // Handle promo code removal
    if ($isRemoval) {
        // Store the original price before removing the promo
        $messageType = trim($_SESSION['form_data']['messageType'] ?? 'default');
        $originalPrice = getPriceByMessageType($messageType);
        
        // Unset the applied promo from session
        unset($_SESSION['applied_promo']);
        unset($_SESSION['got_full_discount']);
        
        // Return success response
        echo json_encode([
            'success' => true,
            'message' => 'Promo code removed successfully',
            'originalPrice' => number_format($originalPrice, 2)
        ]);
        exit;
    }
    
    $promoCode = trim($postData['promoCode']);
    
    if (empty($promoCode)) {
        throw new Exception('Promo code cannot be empty');
    }
    
    // Get DB connection
    $db = Database::getInstance();
    $code = strtoupper($promoCode);
    $now = date('Y-m-d H:i:s');
    
    // 2. Check database if the promo is valid AND hasn't reached max uses in one query
    $stmt = $db->prepare("
        SELECT id, code, discount_percent, discount_amount, max_uses, current_uses 
        FROM promo_codes 
        WHERE code = ? 
        AND is_active = 1 
        AND valid_from <= ? 
        AND valid_until >= ?
        AND (max_uses IS NULL OR current_uses < max_uses)
    ");
    
    $stmt->execute([$code, $now, $now]);
    $promo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$promo) {
        // Combined error message for both invalid/expired and max uses reached
        echo json_encode([
            'success' => false,
            'message' => 'Invalid or expired promo code!'
        ]);
        exit;
    }
    
    // Get original price based on message type
    $messageType = trim($_SESSION['form_data']['messageType'] ?? 'default');
    $originalPrice = getPriceByMessageType($messageType);
    
    // Calculate discount
    $discount = 0;
    if ($promo['discount_percent'] > 0) {
        $discount = $originalPrice * ($promo['discount_percent'] / 100);
    } else if ($promo['discount_amount'] > 0) {
        $discount = $promo['discount_amount'];
    }
    
    // Ensure discount doesn't exceed the original price
    $discount = min($discount, $originalPrice);
    
    // Calculate new price
    $newPrice = max(0, $originalPrice - $discount);
    
    // Store in session
    $_SESSION['applied_promo'] = [
        'code' => $promoCode,
        'discount' => $discount,
        'newPrice' => $newPrice,
        'promo_id' => $promo['id']
    ];
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Promo code applied successfully',
        'newPrice' => number_format($newPrice, 2),
        'originalPrice' => number_format($originalPrice, 2),
        'discount' => number_format($discount, 2)
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage()
    ]);
}
