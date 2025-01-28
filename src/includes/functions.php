<?php

function sendResponse($success, $data) {
    header('Content-Type: application/json');    
    echo json_encode([
        'success' => $success,
        'preview' => $data['preview'] ?? '',
        'version' => $data['version'] ?? 1,
        'remainingVersions' => $data['remainingVersions'] ?? 2,
        'fullContent' => isset($data['isPaid']) && $data['isPaid'] ? ($data['fullContent'] ?? '') : null
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