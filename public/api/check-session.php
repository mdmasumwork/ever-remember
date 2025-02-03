<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../src/services/SessionService.php';

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
