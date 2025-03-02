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
$pageTitle = "Privacy Policy - EverRemember";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body class="privacy-page">
    <?php include __DIR__ . '/../src/templates/header.php'; ?>
    <?php include '../src/templates/slide-menu.php'; ?>
    
    <main class="main-content" id="main-content">
        <section class="step terms-content">
            <h1>Privacy Policy</h1>
            <p class="last-updated">Last Updated: March 3, 2023</p>

            <div class="terms-intro">
                <p>Welcome to Ever Remember. Your privacy is important to us, and we are committed to protecting any personal information you provide. This Privacy Policy explains how we collect, use, and safeguard your data when you use our services.</p>
            </div>
            
            <div class="terms-section">
                <h4>1. Information We Collect</h4>
                <p>We collect only the necessary information required to provide our services:</p>
                <ul>
                    <li><strong>Personal Information:</strong> When you use our service, we may collect your name and email if you provide them for contact purposes.</li>
                    <li><strong>Payment Information:</strong> Payments are securely processed through third-party providers (such as Stripe). We do not store or have access to your credit card details.</li>
                    <li><strong>Session Data:</strong> We temporarily store information in your session (such as input details for generating condolence messages) to enhance user experience.</li>
                </ul>
            </div>
            
            <div class="terms-section">
                <h4>2. How We Use Your Information</h4>
                <p>We use your information only for the following purposes:</p>
                <ul>
                    <li>To generate condolence messages, sympathy letters, obituaries, or eulogies based on user input.</li>
                    <li>To process payments and verify transactions.</li>
                    <li>To respond to inquiries, feedback, or customer support requests.</li>
                    <li>To improve our services by analyzing feedback and usage trends.</li>
                </ul>
            </div>
            
            <div class="terms-section">
                <h4>3. Data Storage & Retention</h4>
                <ul>
                    <li>Generated content is not stored permanently. It is only available in your session until you leave or refresh the page.</li>
                    <li>Payment records and basic user details (such as name and email) may be stored for transaction verification and customer support.</li>
                    <li>We do not share, sell, or distribute your personal data to third parties.</li>
                </ul>
            </div>
            
            <div class="terms-section">
                <h4>4. Cookies & Tracking Technologies</h4>
                <p>We use cookies and session-based tracking to provide a seamless experience. These are used to:</p>
                <ul>
                    <li>Keep track of user input during the session.</li>
                    <li>Prevent unauthorized access or fraudulent activity.</li>
                    <li>Improve website functionality and user experience.</li>
                </ul>
                <p>You can manage or disable cookies in your browser settings, but doing so may affect the functionality of our service.</p>
            </div>
            
            <div class="terms-section">
                <h4>5. Data Security</h4>
                <p>We implement appropriate security measures to protect your information, including:</p>
                <ul>
                    <li><strong>SSL Encryption:</strong> Ensuring secure communication between your browser and our server.</li>
                    <li><strong>Secure Payment Processing:</strong> Payments are handled by trusted third-party providers.</li>
                    <li><strong>Session Security:</strong> Implementing CSRF protection and rate limiting to prevent unauthorized access.</li>
                </ul>
            </div>
            
            <div class="terms-section">
                <h4>6. Third-Party Services</h4>
                <p>We may use third-party services (such as payment providers) to process transactions. These services have their own Privacy Policies, and we recommend reviewing them for more details.</p>
            </div>
            
            <div class="terms-section">
                <h4>7. Your Rights & Choices</h4>
                <p>You have the right to:</p>
                <ul>
                    <li>Request deletion of your personal information (excluding payment records required for compliance).</li>
                    <li>Opt out of receiving emails or promotional messages.</li>
                    <li>Contact us for any privacy-related concerns.</li>
                </ul>
            </div>
            
            <div class="terms-section">
                <h4>8. Changes to This Policy</h4>
                <p>We may update this Privacy Policy from time to time. Any changes will be posted on this page with the last updated date. Your continued use of our service constitutes your acceptance of any modifications.</p>
            </div>
            
            <div class="terms-section">
                <h4>9. Contact Us</h4>
                <p>If you have any questions about this Privacy Policy, please contact us:</p>
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
