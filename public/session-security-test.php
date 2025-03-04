<?php

require_once __DIR__ . '/../src/utils/EnvUtil.php';
require_once __DIR__ . '/../src/utils/SessionSecurityUtil.php';
require_once __DIR__ . '/../src/utils/CSRFUtil.php';
require_once __DIR__ . '/../src/utils/LogUtil.php';

// Check if we're in production - if so, return 404
if (EnvUtil::isProduction()) {
    header("HTTP/1.0 404 Not Found");
    exit("404 Not Found");
}

// Just start session - will automatically use existing session if cookie exists
SessionSecurityUtil::initiateSession();

// Debug output
echo "Session ID: " . session_id() . "<br />";
echo "CSRF Token: " . ($_SESSION['csrf_token'] ?? 'none') . "<br />";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
