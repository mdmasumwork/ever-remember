<?php

require_once __DIR__ . '/../utils/EnvUtil.php';

$isDevelopment = EnvUtil::getEnv('APP_ENV') === 'development';
$baseUrl = EnvUtil::getEnv('BASE_URL');
?>

<!-- Header Step -->
<header id="header" class="sticky-header">
    <div class="logo">
        <a href="<?= $baseUrl ?>" ><img src="assets/image_source_files/logo_9.png" alt="Logo" id="logo"></a>
    </div>
    <nav id="nav" class="main-nav">
        <ul>
            <?php if ($isDevelopment): ?>
                <li><a href="/api/clear-session-with-get.php" target="_blank">Clear Session</a></li>
            <?php endif; ?>
            <li><a href="/">Serenity</a></li>
            <li><a href="/pricing">Pricing</a></li>
            <li><a href="/contact">Contact</a></li>
        </ul>
    </nav>
    <div class="burger-menu" id="burger-menu">
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
    </div>
</header>