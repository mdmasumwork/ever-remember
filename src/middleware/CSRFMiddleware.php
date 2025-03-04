<?php

require_once __DIR__ . '/../utils/CSRFUtil.php';
require_once __DIR__ . '/../utils/LogUtil.php';

class CSRFMiddleware {
    public function handle($page = '') {
        // Skip CSRF check for GET requests
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return true;
        }

        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        
        if (!CSRFUtil::verifyToken($token, $page)) {
            header('HTTP/1.0 403 Forbidden');
            echo json_encode(['error' => 'CSRF token validation failed']);
            LogUtil::log('error', 'CSRF token validation failed');
            exit();
        }
        
        return true;
    }
}
