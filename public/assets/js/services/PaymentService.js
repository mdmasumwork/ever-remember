class PaymentService {
    static async init() {
        try {
            // Fetch Stripe public key from backend
            const data = await HttpService.get('/api/get-stripe-key.php');
            
            if (!data.publicKey) {
                throw new Error('Stripe key not found');
            }

            this.stripe = Stripe(data.publicKey);
            this.elements = this.stripe.elements();
            this.createCardElement();
            this.bindPaymentButton();
            this.bindPromoCodeEvents();
        } catch (error) {
            console.error('Payment initialization failed:', error);
        }
    }

    static async loadFullContent(param = '') {
        const content = await HttpService.get('/api/get-full-content.php?version=1' + param);
        
        if (!content.success) {
            throw new Error(content.error);
        }
        
        return content;
    }

    static async moveToContent_1Step() {
        // Get the button and update its state
        const $button = $('.payment-for-access-button.free-access-button');
        
        // Remove any existing error messages
        $('.payment-error-message').remove();
        
        // Disable button and show loading state
        $button.prop('disabled', true)
            .addClass('processing')
            .text('Processing...');
        
        try {
            // Send request to process the promo code
            const response = await HttpService.post('/api/process-promo-access.php', {});
            
            if (response && response.success) {
                // If successful, proceed with showing the content
                UIManager.updatePaymentUI('continue-after-payment');
                
                try {
                    const content = await this.loadFullContent();
                    UIManager.updateContentStep(content.version, content.fullContent, true);
                } catch (error) {
                    $('.payment-success-overlay').html(`
                        <div class="content-error">
                            <p>Error loading content. Please refresh the page or contact support.</p>
                        </div>
                    `);
                }
            } else {

                $('.promo-code-section .promo-message').text(response?.message || 'Unable to process your promo code. Please try again.').removeClass('success').addClass('error').addClass('visible');
            
                // Restore button state
                $button.prop('disabled', false)
                    .removeClass('processing')
                    .text('Get Your Full Content');
            }
        } catch (error) {
            $('.promo-code-section .promo-message').text('An error occurred while processing your promo code. Please try again.').removeClass('success').addClass('error').addClass('visible');
            
            // Restore button state
            $button.prop('disabled', false)
                .removeClass('processing')
                .text('Get Your Full Content');
        }
    }

    static createCardElement() {
        const style = {
            base: {
                fontSize: '16px',
                color: '#2F394B',
                fontFamily: '"Inter", system-ui, sans-serif',
                '::placeholder': {
                    color: '#9CA3AF',
                },
                padding: '12px 8px',
            },
            invalid: {
                color: '#EF4444',
                iconColor: '#EF4444',
            },
        };

        // Create separate elements
        this.cardNumber = this.elements.create('cardNumber', { style });
        this.cardExpiry = this.elements.create('cardExpiry', { style });
        this.cardCvc = this.elements.create('cardCvc', { style });

        // Mount elements
        this.cardNumber.mount('#card-number-element');
        this.cardExpiry.mount('#card-expiry-element');
        this.cardCvc.mount('#card-cvc-element');
    }

    static bindPaymentButton() {
        $('.payment-overlay .initial-button').on('click', () => {
            UIManager.updatePaymentUI('payment-initiated');
        });

        $('.payment-submit-button').on('click', async (e) => {
            e.preventDefault();
            
            const $button = $(e.currentTarget);
            $button.addClass('processing');
            
            try {
                const data = await HttpService.post('/api/create-payment-intent.php', {});

                if (data.success && data.clientSecret) {
                    const result = await this.stripe.confirmCardPayment(data.clientSecret, {
                        payment_method: {
                            card: this.cardNumber
                        }
                    });

                    if (result.error) {
                        $('#card-errors')
                            .text(result.error.message)
                            .show();
                    } else if (result.paymentIntent.status === 'succeeded') {
                        await this.handlePaymentSuccess(result.paymentIntent);
                    }
                }
            } catch (error) {
                $('#card-errors')
                    .text('Payment failed. Please try again.')
                    .show();
            } finally {
                $button.removeClass('processing').text('Pay Now');
            }
        });
    }

    static bindPromoCodeEvents() {
        // Toggle promo code form visibility when clicking the link
        $('.promo-code-link').on('click', function() {
            $('.promo-code-form').toggleClass('visible');
        });
        
        // Handle promo code application
        $('.promo-code-apply').on('click', async function() {
            const promoCode = $('.promo-code-input').val().trim();
            const $button = $(this);
            
            if (promoCode.length === 0) {
                return;
            }
            
            $button.prop('disabled', true).text('Applying...');
            
            try {
                // Make AJAX call to verify the promo code
                const response = await HttpService.post('/api/apply-promo.php', {
                    promoCode: promoCode
                });
                
                if (response.success) {
                    // Update price display
                    $('.price').text('$' + response.newPrice);
                    
                    // Check if a promo chip already exists
                    const $existingChip = $('.applied-promo-chip');
                    
                    if ($existingChip.length > 0) {
                        // Update the existing chip with new promo code
                        $existingChip.find('.promo-code-text').text('Promo: ' + promoCode.toUpperCase());
                    } else {
                        // Create a new chip if none exists
                        $('.promo-code-section').prepend(`
                            <div class="applied-promo-chip">
                                <span class="promo-code-text">Promo: ${promoCode.toUpperCase()}</span>
                                <span class="promo-code-remove">&times;</span>
                            </div>
                        `);
                        
                        // Bind the remove event
                        PaymentService.bindPromoRemoveEvent();
                    }
                    
                    // Show success message below the promo link
                    $('.promo-code-section .promo-message').text('Promo code applied successfully!').removeClass('error').addClass('success').addClass('visible');
                    
                    // Clear the input for next use
                    $('.promo-code-input').val('');
                    
                    // Add a promo badge next to the price
                    const $promoBadge = $('.promo-badge');
                    if (!$promoBadge.length) {
                        $('.price').append(' <span class="promo-badge">Promo applied</span>');
                    }
                    
                    // If discount is 100%, change the payment button to "Get Your Full Content"
                    if (parseFloat(response.newPrice) === 0) {
                        $('.payment-form').hide();
                        $('.payment-for-access-button')
                            .text('Get Your Full Content')
                            .removeClass('initial-button')
                            .addClass('free-access-button')
                            .show()
                            .off('click')
                            .on('click', async () => {
                                PaymentService.moveToContent_1Step();
                            });
                    } else {
                        // Not free - check if button was previously changed to free access
                        const $paymentButton = $('.payment-for-access-button');
                        if ($paymentButton.hasClass('free-access-button')) {
                            // Change back to regular payment button
                            $paymentButton
                                .text('Purchase to View Full Content')
                                .removeClass('free-access-button')
                                .addClass('initial-button')
                                .off('click')
                                .on('click', () => {
                                    UIManager.updatePaymentUI('payment-initiated');
                                });
                        }
                    }
                } else {
                    $('.promo-code-section .promo-message').text(response.message || 'Invalid promo code!').removeClass('success').addClass('error').addClass('visible');
                }
            } catch (error) {
                $('.promo-code-section .promo-message').text(response.message || 'Invalid promo code!').removeClass('success').addClass('error').addClass('visible');
            } finally {
                $button.prop('disabled', false).text('Apply');
            }
        });
        
        // Also allow Enter key to submit promo code
        $('.promo-code-input').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                $('.promo-code-apply').click();
            }
        });

        $('.payment-for-access-button.free-access-button').on('click', async () => {
            PaymentService.moveToContent_1Step();
        });

        // Bind remove event for any existing promo chips on page load
        this.bindPromoRemoveEvent();
    }

    static bindPromoRemoveEvent() {
        // Delegate the click event for the remove button
        $('.applied-promo-chip .promo-code-remove').off('click').on('click', async function() {
            const $chip = $(this).closest('.applied-promo-chip');
            const promoCode = $chip.find('.promo-code-text').text().replace('Promo: ', '');
            
            try {
                // Show loading state
                $chip.addClass('removing');
                const originalText = $chip.find('.promo-code-text').text();
                $chip.find('.promo-code-text').text('Removing promo code...');
                
                // Show removing message in the message area
                $('.promo-code-section .promo-message').text('Removing promo code...').removeClass('error success').addClass('visible');
                
                // Call the API to remove the promo
                const response = await HttpService.post('/api/apply-promo.php', {
                    promoCode: promoCode,
                    remove: true
                });
                
                if (response.success) {
                    // Remove the chip from UI
                    $chip.remove();
                    
                    // Reset the price to original
                    $('.price').text('$' + response.originalPrice);
                    
                    // Remove the promo badge
                    $('.promo-badge').remove();
                    
                    // Show success message
                    $('.promo-code-section .promo-message').text('Promo code removed successfully!').removeClass('error').addClass('success').addClass('visible');
                    
                    // Check if we need to change the button back to regular payment
                    if ($('.payment-for-access-button').hasClass('free-access-button')) {
                        // Change back to regular payment button
                        $('.payment-for-access-button')
                            .text('Purchase to View Full Content')
                            .removeClass('free-access-button')
                            .addClass('initial-button')
                            .off('click')
                            .on('click', () => {
                                UIManager.updatePaymentUI('payment-initiated');
                            });
                    }
                } else {
                    // Restore chip to normal state
                    $chip.removeClass('removing');
                    $chip.find('.promo-code-text').text(originalText);
                    
                    // Show error message
                    $('.promo-code-section .promo-message').text(response.message || 'Error removing promo code!').removeClass('success').addClass('error').addClass('visible');
                }
            } catch (error) {
                // Restore chip to normal state
                $chip.removeClass('removing');
                $chip.find('.promo-code-text').text(originalText);
                
                console.error('Error removing promo code:', error);
                $('.promo-code-section .promo-message').text('Error removing promo code!').removeClass('success').addClass('error').addClass('visible');
            }
        });
    }

    static async handlePaymentSuccess(paymentIntent) {
        try {
            // 1. Verify payment
            const userName = $('#first-person-name-field').val() || '-';
            const userEmail = $('#email-field').val() || '-';

            const verifyResponse = await HttpService.post('/api/verify-payment.php', {
                paymentIntentId: paymentIntent.id,
                userName: userName,
                userEmail: userEmail
            });

            if (!verifyResponse.success) throw new Error('Payment verification failed');

            // 2. Show success message
            UIManager.updatePaymentUI('payment-success');

            // 3. Handle continue button click
            $('.payment-success-overlay .continue-button').one('click', async () => {
                UIManager.updatePaymentUI('continue-after-payment');
                try {
                    const content = await this.loadFullContent();
                    UIManager.updateContentStep(content.version, content.fullContent, true);
                } catch (error) {
                    $('.payment-success-overlay').html(`
                        <div class="content-error">
                            <p>Error loading content. Please refresh the page or contact support.</p>
                        </div>
                    `);
                }
            });

        } catch (error) {
            $('#card-errors').text(error.message).show();
        }
    }

    
}