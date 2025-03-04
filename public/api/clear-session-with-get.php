<?php

require_once __DIR__ . '/../../src/utils/SessionSecurityUtil.php';
require_once __DIR__ . '/../../src/services/SessionService.php';

header('Content-Type: application/json');

try {
    // First make sure a session is started before trying to clear it
    SessionSecurityUtil::initiateSession();
    
    // Now clear the session
    SessionService::clearSession();
    
    echo json_encode([
        'success' => true,
        'message' => 'Session cleared successfully'
    ]);
    
} catch (Exception $e) {
    error_log('Session clear error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to clear session: ' . $e->getMessage()
    ]);
}
