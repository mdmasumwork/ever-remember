<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once __DIR__ . '/../utils/EnvUtil.php';

$companyName = EnvUtil::getEnv('PRODUCT_NAME') ?? 'Your Company';
$currentYear = date('Y');
?>

<!-- Footer Step -->
<footer id="footer">
    <p>&copy; <?php echo $currentYear; ?> <?php echo htmlspecialchars($companyName); ?>. All rights reserved.</p>
</footer>