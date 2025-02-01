class Accessibility {
    static init() {
        this.bindstepEvents();
        this.bindKeyboardNavigation();
        this.initFirststepFocus();
    }

    static initFirststepFocus() {
        // Focus first element in active step on page load
        const $activestep = $('step.active');
        if ($activestep.length) {
            this.focusFirstElement($activestep[0]);
        }
    }

    static bindstepEvents() {
        // Listen for step activation
        $('step').on('stepActive', function() {
            Accessibility.focusFirstElement(this);
        });
    }

    static focusFirstElement(step) {
        const $step = $(step);
        const stepId = $step.attr('id') || $step.attr('class');

        // Special handling for message-tone step
        if (stepId.includes('message-tone')) {
            const $firstCard = $step.find('.card').first();
            if ($firstCard.length) {
                $firstCard.focus();
                return;
            }
        }

        const $input = $step.find('input, textarea').first();
        if ($input.length) {
            $input.focus();
        } else {
            const $primaryButton = $step.find('.er-button.primary-button').first();
            if ($primaryButton.length) {
                $primaryButton.focus();
            } else {
                const $firstCard = $step.find('.card[tabindex="0"]');
                if ($firstCard.length) {
                    $firstCard.focus();
                }
            }
        }
    }

    static bindKeyboardNavigation() {
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
    }
}