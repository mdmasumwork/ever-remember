<?php

function sendResponse($success, $data) {
    header('Content-Type: application/json');    
    echo json_encode([
        'success' => $success,
        'preview' => isset($data['content']) ? substr($data['content'], 0, 100) . '...' : '',
        'prompt' => $data['prompt'] ?? '',
        'version' => $data['version'] ?? 1,
        'fullContent' => isset($data['payment_verified']) && $data['payment_verified'] ? ($data['content'] ?? '') : null,
        'error' => $data['error'] ?? ''
    ]);
    exit;
}