<?php

require_once __DIR__ . '/../../src/utils/LogUtil.php';
require_once __DIR__ . '/../../src/includes/functions.php';
require_once __DIR__ . '/../../src/services/SessionService.php';
require_once __DIR__ . '/../../src/utils/SecurityHeadersUtil.php';
require_once __DIR__ . '/../../src/middleware/CSRFMiddleware.php';
require_once __DIR__ . '/../../src/utils/EnvUtil.php';
require_once __DIR__ . '/../../src/config/Database.php';

sleep(2); // Simulate processing time

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
    
    // Check if user has a 100% discount from promo code
    if (
        isset($_SESSION['applied_promo']) && 
        isset($_SESSION['applied_promo']['promo_id'])
    ) {
        // Get promo ID from session
        $promoId = $_SESSION['applied_promo']['promo_id'] ?? null;
        
        if ($promoId) {
            // Update the usage counter in the database
            $db = Database::getInstance();
            $updateStmt = $db->prepare("
                UPDATE promo_codes 
                SET current_uses = current_uses + 1 
                WHERE id = ?
            ");
            $updateStmt->execute([$promoId]);
        }
        
        // Set session variable for full discount
        if ($_SESSION['applied_promo']['newPrice'] == 0.0) {
            $_SESSION['got_full_discount'] = true;
            recordFreePromoPayment();
        }        
        
        echo json_encode([
            'success' => true,
            'message' => 'Free access granted successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No valid free promo code found'
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage()
    ]);
}

/**
 * Records a free promo code payment in the payments table
 */
function recordFreePromoPayment() {
    try {
        $db = Database::getInstance();
        
        // Get necessary data from session
        $userName = $_SESSION['form_data']['firstPersonName'] ?? 'Anonymous User';
        $userEmail = $_SESSION['form_data']['email'] ?? null;
        $messageType = $_SESSION['form_data']['messageType'] ?? 'default';
        $promoCode = $_SESSION['applied_promo']['code'] ?? 'UNKNOWN';
        
        // Generate a unique ID for the payment
        $stripeId = 'promo-' . $promoCode . '-' . uniqid();
        
        
        // Insert into payments table with promo_code column
        $stmt = $db->prepare("
            INSERT INTO payments (
                stripe_payment_id, 
                user_name, 
                user_email, 
                amount, 
                payment_method,
                promo_code, 
                content_type,
                status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $stripeId,
            $userName,
            $userEmail,
            0.00,
            'promo_code_full_discount',
            $promoCode, // Add promo code to database record
            $messageType,
            'completed'
        ]);

        // Get the inserted record ID
        $insertedId = $db->lastInsertId();
        $_SESSION['payment_id'] = $insertedId;
        
        LogUtil::log('payment', "Recorded free promo payment: $stripeId for user: $userName, promo: $promoCode");
        
    } catch (Exception $e) {
        LogUtil::log('error', "Failed to record free promo payment: " . $e->getMessage());
        // Don't throw exception - we still want to proceed even if recording fails
    }
}



