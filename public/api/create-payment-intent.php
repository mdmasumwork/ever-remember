<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../src/controllers/PaymentController.php';

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