<?php

// Add error reporting at top
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Fix relative path for require
require_once __DIR__ . '/../../src/controllers/EulogyController.php';

// Rest of the code remains same
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        $controller = new EulogyController();
        $response = $controller->generate($data);
        echo json_encode($response);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'error' => true,
            'message' => $e->getMessage()
        ]);
    }
}