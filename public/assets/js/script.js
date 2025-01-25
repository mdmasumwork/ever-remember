$(document).ready(function(){
    $('.er-button, .card').on('click', function() {
        const classList = $(this).attr('class').split(/\s+/);
        let count = 0;

        classList.forEach(function(className) {
            const match = className.match(/^go-next-(\d+)$/);
            if (match) {
                count = parseInt(match[1], 10);
            }
        });

        if (count > 0) {
            goToNextSection(count);
        }
    });

    // Payment button click handler
    $('.payment-button').on('click', function() {
        const $button = $(this);
        const contentId = $button.data('content-id');
        
        $.ajax({
            url: '/api/process-payment.php',
            method: 'POST',
            data: { contentId: contentId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Update content with full version
                    $('.generated-content').html(response.fullContent);
                    // Remove payment overlay
                    $('.payment-overlay').fadeOut();
                }
            },
            error: function(xhr, status, error) {
                console.error('Payment failed:', error);
            }
        });
    });
});

// Helper function to go to the next section
function goToNextSection(count = 1) {
    const $currentSection = $('section.active');
    let $nextSection = $currentSection.next('section');

    for (let i = 0; i < count - 1; i++) {
        $nextSection = $nextSection.next('section');
    }
    
    if ($nextSection.length === 0) return; // Stop if no next section
    
    $currentSection.removeClass('active');
    $nextSection.addClass('active');
    
    focusInputField($nextSection[0]);
}

// Focus on the input field of the active section
function focusInputField(section) {
    const $section = $(section);
    const $input = $section.find('input, textarea').first();

    if ($input.length) {
        $input.focus();
    } else {
        const $primaryButton = $section.find('.er-button.primary-button').first();
        if ($primaryButton.length) {
            $primaryButton.focus();
        } else {
            const $firstCard = $section.find('.card').first();
            if ($firstCard.length) {
                $firstCard.addClass('focused');
            }
        }
    }
}
