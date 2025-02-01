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

    include '../src/templates/steps/introduction.php';
    include '../src/templates/steps/email.php';
    include '../src/templates/steps/disclaimer.php';
    include '../src/templates/steps/deceased-person-name.php';
    include '../src/templates/steps/message-type.php';
    include '../src/templates/steps/deceased-person-relation.php';
    include '../src/templates/steps/deceased-person-details.php';
    include '../src/templates/steps/deceased-person-accomplishment.php';
    include '../src/templates/steps/message-tone.php';
    include '../src/templates/steps/final-question.php';


    include '../src/templates/steps/content-1.php';
    include '../src/templates/steps/additional-question-1.php';
    include '../src/templates/steps/content-2.php';
    include '../src/templates/steps/additional-question-2.php';
    include '../src/templates/steps/content-3.php';
    include '../src/templates/steps/feedback-1.php';
    include '../src/templates/steps/feedback-2.php';
    include '../src/templates/steps/goodbye.php';

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
<script src="assets/js/modules/step-manager.js"></script>
<script src="assets/js/modules/form-validator.js"></script>
<script src="assets/js/modules/payment.js"></script>
<!-- Main -->
<script src="https://js.stripe.com/v3/"></script>
<script src="assets/js/main.js"></script>
</body>
</html>
