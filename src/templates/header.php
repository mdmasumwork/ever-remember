<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;

// Load environment variables from .env file
$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

$isDevelopment = $_ENV['APP_ENV'] === 'development';
?>

<!-- Header Step -->
<header id="header" class="sticky-header">
    <div class="logo">
        <img src="assets/image_source_files/logo_9.png" alt="Logo" id="logo">
    </div>
    <nav id="nav" class="main-nav">
        <ul>
            <?php if ($isDevelopment): ?>
                <li><a href="/api/clear-session-with-get.php" target="_blank">Clear Session</a></li>
            <?php endif; ?>
            <li><a href="#contact">Contact</a></li>
        </ul>
    </nav>
    <div class="burger-menu" id="burger-menu">
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
    </div>
</header>