<?php
require_once __DIR__ . '/../../src/controllers/PaymentController.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $controller = new PaymentController();
    $result = $controller->verifyAndLogPayment(
        $data['paymentIntentId'],
        $data['userName'],
        $data['userEmail']
    );
    
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}