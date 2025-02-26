<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;

// Load environment variables from .env file
$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

$isDevelopment = $_ENV['APP_ENV'] === 'development';

?>
<!-- Slide Menu -->
<div id="slide-menu" class="slide-menu">
    <span class="close-icon" id="close-menu">&times;</span>
    <nav class="slide-nav">
        <ul>
            <?php if ($isDevelopment): ?>
                <li><a href="/api/clear-session-with-get.php" target="_blank">Clear Session</a></li>
            <?php endif; ?>
            <li><a href="#contact">Contact</a></li>
        </ul>
    </nav>
</div>