<?php
require_once __DIR__ . '/../src/utils/LogUtil.php';
require_once __DIR__ . '/../src/utils/EnvUtil.php';
require_once __DIR__ . '/../src/includes/functions.php';
require_once __DIR__ . '/../src/utils/SessionSecurityUtil.php';
require_once __DIR__ . '/../src/utils/SecurityHeadersUtil.php';

// Initialize session and security
SessionSecurityUtil::initiateSession();
SecurityHeadersUtil::setIndexHeaders('GET');
SecurityHeadersUtil::handlePreflight('GET');

// Reject non-GET requests at the page level
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo "Method Not Allowed";
    exit;
}

// Page title
$pageTitle = "Terms and Conditions - EverRemember";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body class="terms-page">
    <?php include __DIR__ . '/../src/templates/header.php'; ?>
    <?php include '../src/templates/slide-menu.php'; ?>
    
    <main class="main-content" id="main-content">
        <section class="step terms-content">
            <h1>Terms and Conditions</h1>
            <p class="last-updated">Last Updated: March 3, 2025</p>

            <div class="terms-intro">
                <p>Welcome to Ever Remember, an AI-powered condolence and sympathy message generation service. By accessing or using our website, you agree to be bound by the following terms and conditions. Please read them carefully before using our services.</p>
            </div>
            
            <div class="terms-section">
                <h4>1. Acceptance of Terms</h4>
                <p>By using Ever Remember, you acknowledge that you have read, understood, and agree to comply with these Terms and Conditions. If you do not agree, please do not use our services.</p>
            </div>
            
            <div class="terms-section">
                <h4>2. Services Provided</h4>
                <p>Ever Remember helps users generate condolence messages, sympathy letters, obituaries, and eulogies using AI-powered assistance. The content provided is for personal use only and should not be considered legal, professional, or medical advice.</p>
            </div>
            
            <div class="terms-section">
                <h4>3. User Responsibilities</h4>
                <p>When using our services, you agree that you will:</p>
                <ul>
                    <li>Provide accurate and lawful information.</li>
                    <li>Not use the service for fraudulent, offensive, or illegal activities.</li>
                    <li>Not attempt to copy, resell, or exploit the AI-generated content for commercial use without permission.</li>
                </ul>
            </div>
            
            <div class="terms-section">
                <h4>4. Payments & Refunds</h4>
                <p>Payments for using Ever Remember are processed securely through third-party payment providers such as Stripe.</p>
                <p>All transactions are final and non-refundable, except in cases where a billing error has occurred. If you believe a charge was made incorrectly, please contact us at support@everremember.com within 7 days of the transaction.</p>
            </div>
            
            <div class="terms-section">
                <h4>5. Content & Data Storage</h4>
                <p>We store user-provided details, including name, email, payment transaction details, and feedback, for record-keeping and customer support.</p>
                <p>AI-generated content is not stored permanently and is only accessible during an active session.</p>
                <p>We do not share or sell user data to third parties.</p>
            </div>
            
            <div class="terms-section">
                <h4>6. Privacy & Security</h4>
                <p>We are committed to protecting your privacy. Our <a href="/privacy">Privacy Policy</a> explains how we collect, use, and safeguard your information.</p>
                <p>We do not share personal data with third parties except as required for processing payments or complying with legal obligations.</p>
            </div>
            
            <div class="terms-section">
                <h4>7. Limitation of Liability</h4>
                <p>Ever Remember is provided on an "as-is" basis. While we strive to generate meaningful and high-quality messages, AI-generated content is inherently subjective, and we cannot guarantee that it will meet all individual preferences.</p>
                <p>By using our service, you acknowledge that the generated content is based on the information provided and may not always reflect the exact sentiment or wording you desire. If you have concerns, please contact us, and we will do our best to assist you.</p>
                <p>Ever Remember shall not be liable for any emotional distress, inaccuracies, or consequences resulting from the use of our AI-generated content.</p>
            </div>
            
            <div class="terms-section">
                <h4>8. Modifications to Terms</h4>
                <p>We may update these Terms and Conditions from time to time. Any changes will be posted on this page with the last updated date. Continued use of the service after updates constitutes your acceptance of the revised terms.</p>
            </div>
            
            <div class="terms-section">
                <h4>9. Contact Information</h4>
                <p>If you have any questions or concerns regarding these Terms and Conditions, please contact us at:</p>
                <p class="email-address"><img src="/assets/images/icons/email-orange.svg" /> <?= EnvUtil::getEnv('SUPPORT_EMAIL', 'md.masum.dev@gmail.com') ?></p>
            </div>
        </section>
    </main>
    
    <?php include __DIR__ . '/../src/templates/footer.php'; ?>


    <script 
        src="https://code.jquery.com/jquery-3.7.1.min.js" 
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" 
        crossorigin="anonymous">
    </script>
    <script src="assets/js/components/SlideMenu.js"></script>
    <script src="assets/js/termsAndCondition.js"></script>
</body>
</html>
