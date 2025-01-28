<?php
require_once __DIR__ . '/../../src/includes/functions.php';
require_once __DIR__ . '/../../src/controllers/ContentController.php';

session_start();

// Verify payment status
if (!isset($_SESSION['payment_verified']) || !$_SESSION['payment_verified']) {
    return sendResponse(false, ['error' => 'Payment required']);
}

// Check if content exists
if (!isset($_SESSION['full_content'])) {
    return sendResponse(false, ['error' => 'Content not found']);
}

// Use same response format
return sendResponse(true, [
    'preview' => '',  // Empty since we're only sending full content
    'fullContent' => $_SESSION['full_content'],
    'isPaid' => true
]);