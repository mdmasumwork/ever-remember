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

// Check if version is set
if (!isset($_GET['version'])) {
    return sendResponse(false, ['error' => 'Version not specified']);
}

$version = $_GET['version'];

// Check if the requested version exists
if (!isset($_SESSION['contents'][$version])) {
    return sendResponse(false, ['error' => 'This version of content is not found']);
}

// Use same response format
return sendResponse(true, [
    'preview' => '',  // Empty since we're only sending full content
    'fullContent' => $_SESSION['contents'][$version],
    'isPaid' => true,
    'version' => $version
]);