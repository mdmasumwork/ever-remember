<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../src/controllers/FeedbackController.php';

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
