class Payment {
    static init() {
        this.bindEvents();
    }

    static bindEvents() {
        $('.payment-button').on('click', function() {
            const $button = $(this);
            const contentId = $button.data('content-id');
            
            Payment.processPayment(contentId);
        });
    }

    static processPayment(contentId) {
        $.ajax({
            url: '/api/process-payment.php',
            method: 'POST',
            data: { contentId: contentId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('.generated-content').html(response.fullContent);
                    $('.payment-overlay').fadeOut();
                }
            },
            error: function(xhr, status, error) {
                console.error('Payment failed:', error);
            }
        });
    }
}