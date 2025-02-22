<?php

function sendResponse($success, $data) {
    header('Content-Type: application/json');    

    $content = $data['content'] ?? '';
    $previewLength = strlen($content) > 0 ? ceil(strlen($content) * 0.40) : 0;
    $preview = $previewLength > 0 ? substr($content, 0, $previewLength) . '...' : '';

    echo json_encode([
        'success' => $success,
        'preview' => $preview,
        'prompt' => $data['prompt'] ?? '',
        'version' => $data['version'] ?? 1,
        'fullContent' => isset($data['payment_verified']) && $data['payment_verified'] ? ($content ?? '') : null,
        'error' => $data['error'] ?? ''
    ]);
    exit;
}