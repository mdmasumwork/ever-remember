<section class="section section-content-1">
    <h4>Thanks for sharing those beautiful details. Here's what I've created...</h4>
    
    <div class="content-tabs">
        <div class="tab-navigation">
            <button class="tab-button active" data-tab="version1-content1">Version 1</button>
        </div>

        <div class="tab-content">
            <div class="tab-pane version-1 active">
                <!-- Loading State -->
                <div class="loading-indicator">
                    <div class="loading-text">
                        <h4>Creating your personalized content</h4>
                        <div class="loading-dots">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                </div>

                <!-- Content Box -->
                <div class="content-box masked">
                    <div class="generated-content" style="display: none;">
                        <!-- Content will be inserted here by JavaScript -->
                    </div>
                </div>
                <div class="content-copy-actions">
                    <img class="copy-icon" src="assets/images/icons/letter-orange.svg">
                    <img class="email-icon" src="assets/images/icons/email-orange.svg">
                </div>
            </div>
            
            <div class="content-actions">
                <div class="payment-overlay">
                    <h5>Unlock your complete personalized eulogy</h5>
                    <p class="price">$9.99</p>
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

                <div class="feedback-section" style="display: none;">
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