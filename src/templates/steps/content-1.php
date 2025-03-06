<?php

require_once __DIR__ . '/../../utils/EnvUtil.php';

$messageType = trim($_SESSION['form_data']['messageType'] ?? 'default');
echo "<h1>Message Type: $messageType</h1>";

if ($messageType === 'condolence message') {
    $amount = EnvUtil::getEnv('CONDOLENCE_PRICE', '9.99');
} else if ($messageType === 'sympathy letter') {
    $amount = EnvUtil::getEnv('SYMPATHY_LETTER_PRICE', '9.99');
} else if ($messageType === 'eulogy') {
    $amount = EnvUtil::getEnv('EULOGY_PRICE', '9.99');
} else if ($messageType === 'obituary') {
    $amount = EnvUtil::getEnv('OBITUARY_PRICE', '9.99');
} else {
    $amount = EnvUtil::getEnv('CONTENT_PRICE', '9.99');
    $messageType = 'personalized content';
}

?>
<section class="step step-content-1">
    <h4>Thanks for sharing those beautiful details. Here's what I've created...</h4>
    
    <div class="content-tabs">
        <div class="tab-navigation">
            <button class="tab-button active" data-tab="version1-content1">Version 1</button>
        </div>

        <div class="tab-content">
            <div class="tab-pane version-1 active">
                <!-- Loading State -->
                <div class="loading-indicator visible">
                    <div class="loading-text">
                        <h4>Creating your personalized content</h4>
                        <div class="loading-dots">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                </div>

                <div class="error-message"></div>

                <!-- Content Box -->
                <div class="content-box">
                    <div class="generated-content">
                    </div>
                </div>
                <div class="content-copy-actions">
                    <img class="copy-icon" src="assets/images/icons/letter-orange.svg">
                    <img class="email-icon" src="assets/images/icons/email-orange.svg">
                </div>
            </div>

            <div class="toast-message">
                <p>Toast</p>
            </div>

            <div class="content-actions">
                <div class="payment-overlay">
                    <h5>Unlock your <span class="message-type-placeholder"><?= $messageType ?></span></h5>
                    <p class="price">$<?= $amount ?></p>
                    <p>See our <a href="/pricing" target="_blank">pricing</a> for more details.</p>
                    <p class="green-text">After payment, you will also have the opportunity to modify the content two more times.</p>
                    <button class="payment-button initial-button">Purchase to View Full Content</button>

                    <div class="payment-form" style="display: none;">
                        
                        <div class="card-element-container">
                            <div id="card-number-element" class="stripe-element"></div>
                            <div class="row">
                                <div id="card-expiry-element" class="stripe-element"></div>
                                <div id="card-cvc-element" class="stripe-element"></div>
                            </div>
                            <div id="card-errors" role="alert"></div>
                        </div>

                        <div class="payment-form-actions">
                            <button class="payment-button payment-submit-button">Pay Now</button>
                        </div>
                        
                        <div class="security-badges">
                            <a href="https://stripe.com" target="_blank" rel="noopener noreferrer">
                                <img src="assets/images/powered_by_stripe.png" alt="Powered by Stripe">
                            </a>
                        </div>
                    </div>
                </div>
                <div class="payment-success-overlay">
                    <div class="success-content">
                        <div class="success-animation">
                            <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                                <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
                                <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                            </svg>
                        </div>
                        <h3>Payment Successful!</h3>
                        <p>Thank you for your payment.</p>
                        <button class="er-button primary-button continue-button">Continue to View Content</button>
                    </div>
                </div>

                <div class="content-action">
                    <p class="action-question">How do you feel about this content?</p>
                    <div class="action-buttons">
                        <button class="er-button primary-button go-next-5">I like it</button>
                        <button class="er-button secondary-button go-next-1">I'd like some changes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>