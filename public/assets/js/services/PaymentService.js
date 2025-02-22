class PaymentService {
    static async init() {
        try {
            // Fetch Stripe public key from backend
            const data = await HttpService.get('/api/get-stripe-key.php');

            console.log('Stripe key:', data.publicKey);
            
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

    static async handlePaymentSuccess(paymentIntent) {
        try {
            // 1. Verify payment
            const userName = $('#first-person-name-field').val() || '-';
            const userEmail = $('#email-field').val() || '-';

            console.log('Name:', userName);

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

    static async loadFullContent() {
        const content = await HttpService.get('/api/get-full-content.php?version=1');
        
        if (!content.success) {
            throw new Error(content.error);
        }
        
        return content;
    }
}