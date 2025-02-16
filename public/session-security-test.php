<?php

require_once __DIR__ . '/../src/utils/SessionSecurityUtil.php';
require_once __DIR__ . '/../src/utils/CSRFUtil.php';

// Just start session - will automatically use existing session if cookie exists
SessionSecurityUtil::initiateSession();

// Debug output
echo "Session ID: " . session_id() . "<br />";
echo "CSRF Token: " . ($_SESSION['csrf_token'] ?? 'none') . "<br />";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
