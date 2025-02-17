<?php

//error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once __DIR__ . '/../src/utils/EnvUtil.php';
require_once __DIR__ . '/../src/utils/SessionSecurityUtil.php';
require_once __DIR__ . '/../src/utils/DebugUtil.php';
require_once __DIR__ . '/../src/utils/sanitize.php';
require_once __DIR__ . '/../src/utils/CSRFUtil.php';

// Load environment variables
EnvUtil::loadEnvFile();

// Start secure session
SessionSecurityUtil::initiateSession();

// Generate CSRF token
$csrfToken = CSRFUtil::generateToken();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo $csrfToken; ?>">
    <title>Memorial Content Generator</title>
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>
<!-- Include Header -->
<?php include '../src/templates/header.php'; ?>
<?php include '../src/templates/slide-menu.php'; ?>
<?php include '../src/templates/overlays/session-restore.php'; ?>

<main id="main-content">
    <?php include '../src/templates/progress-bar.php'; ?>
    
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


<!-- Services -->
<script src="assets/js/services/SessionService.js"></script>
<script src="assets/js/services/PaymentService.js"></script>
<script src="assets/js/services/HttpService.js"></script>
<script src="assets/js/services/ValidationService.js"></script>
<script src="assets/js/services/EmailService.js"></script>

<!-- Components -->
<script src="assets/js/components/Accessibility.js"></script>
<script src="assets/js/components/SlideMenu.js"></script>
<script src="assets/js/components/Toast.js"></script>
<script src="assets/js/components/Tabs.js"></script>
<script src="assets/js/components/ProgressBar.js"></script>

<!-- Modules -->
<script src="assets/js/modules/UIManager.js"></script>
<script src="assets/js/modules/DataManager.js"></script>
<script src="assets/js/modules/StepManager.js"></script>

<!-- Main -->
<script src="https://js.stripe.com/v3/"></script>
<script src="assets/js/main.js"></script>
</body>
</html>
