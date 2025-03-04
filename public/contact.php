<?php
require_once __DIR__ . '/../src/utils/LogUtil.php';
require_once __DIR__ . '/../src/includes/functions.php';
require_once __DIR__ . '/../src/utils/SessionSecurityUtil.php';
require_once __DIR__ . '/../src/utils/CSRFUtil.php';
require_once __DIR__ . '/../src/utils/SecurityHeadersUtil.php';
require_once __DIR__ . '/../src/middleware/CSRFMiddleware.php';
require_once __DIR__ . '/../src/middleware/RateLimitMiddleware.php';

// Initialize session and security - following index.php pattern
SessionSecurityUtil::initiateSession();
SecurityHeadersUtil::setIndexHeaders('GET');
SecurityHeadersUtil::handlePreflight('GET');
$csrf = new CSRFMiddleware();
$csrf->handle('contact');
$rateLimitMiddleware = new RateLimitMiddleware();
$rateLimitMiddleware->handle('contact');
$csrfToken = CSRFUtil::generateToken('contact');

// Reject non-GET requests at the page level
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo "Method Not Allowed";
    exit;
}

// Page title
$pageTitle = "Contact Us";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo $csrfToken; ?>">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body class="contact-page">
    <?php include __DIR__ . '/../src/templates/header.php'; ?>
    <?php include '../src/templates/slide-menu.php'; ?>
    
    <main class="main-content" id="main-content">
        <section class="step step-contact-form">
            <h1>Feel Free to Reach Out to Us</h1>
            <p class="lead">Have a question, suggestion, or interested in working with us? We'd love to hear from you! Please fill out the form below, and we'll get back to you as soon as possible.</p>
            
            <div id="form-messages" class="alert" style="display: none;"></div>
            
            <div id="contact-form-container">
                <form id="contractor-contact-form" class="contact-form">
                    <div class="field-area">
                        <input type="text" id="name" name="name" placeholder="Your Full Name *" required>
                    </div>
                    
                    <div class="field-area">
                        <input type="email" id="email" name="email" placeholder="Your Email *" required>
                    </div>
                    
                    <div class="field-area">
                        <input type="tel" id="phone" name="phone" placeholder="Your Phone Number">
                    </div>
                    
                    <div class="field-area">
                        <input type="text" id="company" name="company" placeholder="Your Company">
                    </div>
                    
                    <div class="field-area">
                        <textarea id="message" name="message" rows="5" placeholder="Your Message *" required></textarea>
                    </div>
                    
                    <div class="form-submit-button-area">
                        <button type="submit" class="er-button primary-button">Submit</button>
                        <div class="spinner" style="display: none;">
                            <span class="spinner-text">Submitting</span>
                            <span class="dot"></span>
                            <span class="dot"></span>
                            <span class="dot"></span>
                        </div>
                    </div>
                </form>
            </div>
            
            <div id="success-container" style="display: none;">
                <div class="alert alert-success green-text">
                    Thank you for contacting us! We will get back to you shortly.
                </div>
                <p class="mt-4">
                    <button id="reset-form-btn" class="er-button primary-button">Submit Another Inquiry</button>
                </p>
            </div>
        </section>
    </main>
    
    <?php include __DIR__ . '/../src/templates/footer.php'; ?>
    
    <!-- Scripts -->
    <script 
        src="https://code.jquery.com/jquery-3.7.1.min.js" 
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" 
        crossorigin="anonymous">
    </script>

    <script src="assets/js/components/SlideMenu.js"></script>
    <script src="/assets/js/services/HttpService.js"></script>
    <script src="/assets/js/modules/ContactForm.js"></script>
</body>
</html>
