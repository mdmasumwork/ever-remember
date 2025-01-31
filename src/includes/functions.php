<?php

function sendResponse($success, $data) {
    header('Content-Type: application/json');    
    echo json_encode([
        'success' => $success,
        'preview' => $data['preview'] ?? '',
        'version' => $data['version'] ?? 1,
        'fullContent' => isset($data['payment_verified']) && $data['payment_verified'] ? ($data['fullContent'] ?? '') : null,
        'error' => $data['error'] ?? ''
    ]);
    exit;
}

function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

function validateSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['baseAnswers']);
}