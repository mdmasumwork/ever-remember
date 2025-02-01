<?php
require_once '../src/utils/validation.php'; // Validation utility
require_once '../src/utils/sanitize.php'; // Sanitization utility

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memorial Content Generator</title>
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>
<!-- Include Header -->
<?php include '../src/templates/header.php'; ?>
<?php include '../src/templates/slide-menu.php'; ?>

<main id="main-content">
    
    
    <?php

    include '../src/templates/sections/introduction.php';
    include '../src/templates/sections/email-section.php';
    include '../src/templates/sections/disclaimer-section.php';
    include '../src/templates/sections/deceased-person-name-section.php';
    include '../src/templates/sections/message-type-section.php';
    include '../src/templates/sections/deceased-person-relation-section.php';
    include '../src/templates/sections/deceased-person-details-section.php';
    include '../src/templates/sections/deceased-person-accomplishment.php';
    include '../src/templates/sections/message-tone-section.php';
    include '../src/templates/sections/final-question-section.php';


    include '../src/templates/sections/content-section-1.php';
    include '../src/templates/sections/additional-question-1.php';
    include '../src/templates/sections/content-section-2.php';
    include '../src/templates/sections/additional-question-2.php';
    include '../src/templates/sections/content-section-3.php';
    include '../src/templates/sections/feedback-section.php';
    include '../src/templates/sections/feedback-section-2.php';
    include '../src/templates/sections/goodbye-section.php';

    ?>
    
</main>

<!-- Include Footer -->
<?php include '../src/templates/footer.php'; ?>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Components -->
<script src="assets/js/components/accessibility.js"></script>
<script src="assets/js/components/slide-menu.js"></script>
<script src="assets/js/components/toast.js"></script>
<script src="assets/js/components/tabs.js"></script>
<script src="assets/js/components/card.js"></script>
<!-- Modules -->
<script src="assets/js/modules/ui-manager.js"></script>
<script src="assets/js/modules/data-manager.js"></script>
<script src="assets/js/modules/section-manager.js"></script>
<script src="assets/js/modules/form-validator.js"></script>
<script src="assets/js/modules/payment.js"></script>
<!-- Main -->
<script src="https://js.stripe.com/v3/"></script>
<script src="assets/js/main.js"></script>
</body>
</html>
