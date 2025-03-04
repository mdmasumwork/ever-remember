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

// Get pricing from environment variables
$condolencePrice = EnvUtil::getEnv('CONDOLENCE_MESSAGE_PRICE', '5.99');
$sympathyLetterPrice = EnvUtil::getEnv('SYMPATHY_LETTER_PRICE', '10.99');
$eulogyPrice = EnvUtil::getEnv('EULOGY_PRICE', '15.99');
$obituaryPrice = EnvUtil::getEnv('OBITUARY_PRICE', '15.99');

// Page title
$pageTitle = "Pricing - EverRemember";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body class="pricing-page">
    <?php include __DIR__ . '/../src/templates/header.php'; ?>
    <?php include '../src/templates/slide-menu.php'; ?>
    
    <main class="main-content" id="main-content">
        <section class="step pricing-content">
            <h1>Simple, Transparent Pricing</h1>
            <p class="pricing-intro">Choose the service that best fits your needs. Our AI-powered tools help you create heartfelt messages during difficult times.</p>

            <div class="pricing-grid">
                <!-- Condolence Message Card -->
                <div class="pricing-card" data-package="condolence">
                    <div class="pricing-card-header">
                        <h4>Condolence Message</h4>
                        <div class="pricing-amount">
                            <span class="currency">$</span>
                            <span class="price"><?= $condolencePrice ?></span>
                        </div>
                        <p class="pricing-description">A brief, personalized message expressing sympathy and support</p>
                    </div>
                    <div class="pricing-card-body">
                        <ul class="pricing-features">
                            <li>Personalized condolence message</li>
                            <li>3 AI-generated variations</li>
                            <li>Perfect for social media, sympathy cards, or flower notes</li>
                        </ul>
                    </div>
                    <div class="pricing-card-footer">
                        <button class="er-button primary-button select-plan-btn">Get Started</button>
                    </div>
                </div>

                <!-- Sympathy Letter Card -->
                <div class="pricing-card" data-package="sympathy">
                    <div class="pricing-card-header">
                        <h4>Sympathy Letter</h4>
                        <div class="pricing-amount">
                            <span class="currency">$</span>
                            <span class="price"><?= $sympathyLetterPrice ?></span>
                        </div>
                        <p class="pricing-description">A thoughtful, detailed letter expressing your deepest sympathies</p>
                    </div>
                    <div class="pricing-card-body">
                        <ul class="pricing-features">
                            <li>A thoughtful, AI-personalized sympathy letter</li>
                            <li>3 AI-generated variations</li>
                            <li>Formatted for printing or email sending</li>
                            <li>Helps express deep sympathy when words are hard to find</li>
                        </ul>
                    </div>
                    <div class="pricing-card-footer">
                        <button class="er-button primary-button select-plan-btn">Get Started</button>
                    </div>
                </div>

                <!-- Eulogy Card -->
                <div class="pricing-card featured" data-package="eulogy">
                    <div class="pricing-card-badge">Most Popular</div>
                    <div class="pricing-card-header">
                        <h4>Eulogy</h4>
                        <div class="pricing-amount">
                            <span class="currency">$</span>
                            <span class="price"><?= $eulogyPrice ?></span>
                        </div>
                        <p class="pricing-description">A heartfelt speech celebrating the life of your loved one</p>
                    </div>
                    <div class="pricing-card-body">
                        <ul class="pricing-features">
                            <li>Comprehensive, structured eulogy speech</li>
                            <li>3 AI-generated variations</li>
                            <li>Personalized with your memories</li>
                            <li>Perfect for funeral speeches, memorials, and tributes</li>
                        </ul>
                    </div>
                    <div class="pricing-card-footer">
                        <button class="er-button primary-button select-plan-btn">Get Started</button>
                    </div>
                </div>

                <!-- Obituary Card -->
                <div class="pricing-card" data-package="obituary">
                    <div class="pricing-card-header">
                        <h4>Obituary</h4>
                        <div class="pricing-amount">
                            <span class="currency">$</span>
                            <span class="price"><?= $obituaryPrice ?></span>
                        </div>
                        <p class="pricing-description">A respectful announcement of passing with biographical details</p>
                    </div>
                    <div class="pricing-card-body">
                        <ul class="pricing-features">
                            <li>Professional obituary</li>
                            <li>3 AI-generated variations</li>
                            <li>Formatted for newspapers, websites, or memorial programs</li>
                            <li>Creates a heartfelt tribute with biographical details</li>
                        </ul>
                    </div>
                    <div class="pricing-card-footer">
                        <button class="er-button primary-button select-plan-btn">Get Started</button>
                    </div>
                </div>
            </div>

            <div class="pricing-guarantee">
                <h3>Our Promise</h3>
                <p>We understand how important it is to express the right words during difficult times. Our AI generates heartfelt content based on the details you provide. Before payment, you’ll see a preview of the message to ensure it captures the right sentiment. If you’re not satisfied after receiving the full content, you can <strong>request up to two revisions</strong> with additional details or instructions to refine it. We’re here to help you craft the perfect message.</p>
            </div>

            <div class="pricing-faq">
                <h3>Frequently Asked Questions</h3>
                
                <div class="faq-item">
                    <h4>How does the process work?</h4>
                    <p>After selecting a service, you’ll answer a few questions about your loved one. Our AI will generate a personalized message based on your input. Before payment, you'll see a <strong>short preview</strong> to ensure the content feels meaningful. Once you complete your purchase, you'll receive the <strong>full content immediately</strong>, along with the option to request <strong>two revisions</strong> to further refine the message.</p>
                </div>
                
                <div class="faq-item">
                    <h4>Can I edit the generated content?</h4>
                    <p>Yes! After purchasing, you will have <strong>two opportunities to refine the content</strong>. You can provide additional details, request changes, or adjust the wording to better reflect your emotions. Our AI will then regenerate the content based on your updates.</p>
                </div>
                
                <div class="faq-item">
                    <h4>How quickly will I receive my content?</h4>
                    <p>The <strong>first version</strong> of your content is generated <strong>instantly</strong> and available for preview before payment. Once you complete the purchase, you'll gain full access to it <strong>immediately</strong>. If you request revisions, the updated content will be generated instantly. Additionally, you will have the option to <strong>send the final content to your email</strong> just by clicking a button.</p>
                </div>
            </div>
        </section>
    </main>
    
    <?php include __DIR__ . '/../src/templates/footer.php'; ?>

    <script 
        src="https://code.jquery.com/jquery-3.7.1.min.js" 
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" 
        crossorigin="anonymous">
    </script>
    <script src="/assets/js/components/SlideMenu.js"></script>
    <script src="/assets/js/pricing.js"></script>
</body>
</html>
