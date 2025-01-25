$(document).ready(function() {
    // Auto focus first card when section becomes active
    $('section').on('sectionActive', function() {
        $(this).find('.card[tabindex="0"]').focus();
    });

    // Handle keyboard navigation for cards
    $('.card').on('keydown', function(e) {
        const $cards = $('.card');
        const currentIndex = $cards.index(this);
        
        switch(e.keyCode) {
            case 13: // Enter
            case 32: // Space
                e.preventDefault();
                $(this).trigger('click');
                break;
                
            case 38: // Up
            case 37: // Left
                e.preventDefault();
                if (currentIndex > 0) {
                    $cards.eq(currentIndex - 1).focus();
                }
                break;

            case 40: // Down
            case 39: // Right
                e.preventDefault();
                if (currentIndex < $cards.length - 1) {
                    $cards.eq(currentIndex + 1).focus();
                }
                break;
        }
    });
});