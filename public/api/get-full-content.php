<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../src/includes/functions.php';
require_once __DIR__ . '/../../src/controllers/ContentController.php';

session_start();

// sleep(1);

try {
    $version = isset($_GET['version']) ? (int)$_GET['version'] : null;
    
    if (!$version) {
        throw new Exception('Version parameter is required');
    }
    
    if (!isset($_SESSION['contents'][$version])) {
        throw new Exception('Content not found for this version');
    }
    
    if (!isset($_SESSION['payment_verified']) || !$_SESSION['payment_verified']) {
        throw new Exception('Payment verification required');
    }
    
    echo json_encode([
        'success' => true,
        'fullContent' => $_SESSION['contents'][$version],
        'version' => $version
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}