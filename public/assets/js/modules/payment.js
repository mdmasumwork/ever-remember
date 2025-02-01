class Payment {
    static async init() {
        try {
            // Fetch Stripe public key from backend
            const response = await fetch('/api/get-stripe-key.php');
            const data = await response.json();
            
            if (!data.publicKey) {
                throw new Error('Stripe key not found');
            }

            this.stripe = Stripe(data.publicKey);
            this.elements = this.stripe.elements();
            this.createCardElement();
            this.bindPaymentButton();
        } catch (error) {
            console.error('Payment initialization failed:', error);
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
        $('.initial-button').on('click', () => {
            $('.initial-button').hide();
            $('.payment-form').slideDown();
        });

        $('.payment-submit-button').on('click', async (e) => {
            e.preventDefault();
            
            const $button = $(e.currentTarget);
            $button.addClass('processing');
            
            try {
                const response = await fetch('/api/create-payment-intent.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });

                const data = await response.json();

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

    static async handlePaymentSuccess(paymentIntent) {
        try {
            // 1. Verify payment
            const userName = $('#first-person-name-field').val() || '-';
            const userEmail = $('#email-field').val() || '-';

            console.log('Name:', userName);

            const verifyResponse = await fetch('/api/verify-payment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    paymentIntentId: paymentIntent.id,
                    userName: userName,
                    userEmail: userEmail
                })
            });

            if (!verifyResponse.ok) throw new Error('Payment verification failed');

            // 2. Show success message
            $('.payment-overlay').hide();
            $('.payment-success-overlay').addClass('visible');

            // 3. Handle continue button click
            $('.payment-success-overlay .continue-button').one('click', async () => {
                try {
                    const content = await this.loadFullContent();
                    UIManager.updateContentSection(content.fullContent, content.version, true);
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

    static async loadFullContent() {
        const response = await fetch('/api/get-full-content.php?version=1');
        const content = await response.json();
        
        if (!content.success) {
            throw new Error(content.error);
        }
        
        return content;
    }
}