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

/**
 * Get the price corresponding to the message type from environment variables
 * 
 * @param string $messageType The type of message (condolence message, sympathy letter, eulogy, obituary)
 * @return string The price for the specified message type
 */
function getPriceByMessageType($messageType) {
    $messageType = strtolower($messageType);
    
    switch ($messageType) {
        case 'condolence message':
            return EnvUtil::getEnv('CONDOLENCE_MESSAGE_PRICE', '5.99');
        case 'sympathy letter':
            return EnvUtil::getEnv('SYMPATHY_LETTER_PRICE', '12.99');
        case 'eulogy':
            return EnvUtil::getEnv('EULOGY_PRICE', '19.99');
        case 'obituary':
            return EnvUtil::getEnv('OBITUARY_PRICE', '19.99');
        default:
            // Default to condolence price if no match
            return EnvUtil::getEnv('CONDOLENCE_MESSAGE_PRICE', '5.99');
    }
}