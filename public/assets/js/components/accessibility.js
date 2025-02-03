class Accessibility {
    static init() {
        this.bindStepForwardTriggers();
        this.bindCardInteractions();
        this.focusActiveStepElement();
    }

    static bindStepForwardTriggers() {
        // Handle button clicks with go-next-{number} class
        $(document).on('click', '[class*="go-next-"]', function(e) {            
            Accessibility.triggerStepForwardEvent(this);
        });

        // Handle Enter key on elements with both go-next-{number} and card classes
        $(document).on('keypress', '[class*="go-next-"].card', function(e) {
            if (e.which === 13) { // Enter key
            Accessibility.triggerStepForwardEvent(this);
            }
        });

        // Handle Enter key on input fields (excluding textarea)
        $(document).on('keypress', 'input', function(e) {
            if (e.which === 13) { // Enter key
            const $submitButton = $(this).closest('.step').find('button[type="submit"]');
            if ($submitButton.length) {
                Accessibility.triggerStepForwardEvent($submitButton.get(0));
            } else {
                $(document).trigger('stepForwardRequested', [1]);
            }
            }
        });
    }

    static triggerStepForwardEvent(element) {

        if ($(element).hasClass('card')) {
            $(element).addClass('selected').siblings().removeClass('selected');
        }

        const classList = $(element).attr('class').split(/\s+/);
        let steps = 0;
        let skip = false;

        classList.forEach(function(className) {
            const match = className.match(/^go-next-(\d+)$/);
            if (match) {
                steps = parseInt(match[1], 10);
            }
            if (className === 'skip-button') {
                skip = true;
            }
        });

        if (steps > 0) {
            $(document).trigger('stepForwardRequested', [steps, skip]);
        }
    }

    static focusActiveStepElement() {
        const $activeStep = $('.step.active');
        if (!$activeStep.length) return;

        // If it's the message type step, focus first card
        if ($activeStep.hasClass('card-step')) {
            const $firstCard = $activeStep.find('.card').first();
            if ($firstCard.length) {
                $firstCard.attr('tabindex', '0').focus();
            }
            return;
        } else {
            // Try to find the first interactive element
            let $focusElement = $activeStep.find('input:visible, textarea:visible').first();
            if (!$focusElement.length) {
                $focusElement = $activeStep.find('.er-button').first();
            }

            if ($focusElement.length) {
                // Clear any existing content and prevent default input
                if ($focusElement.is('textarea')) {
                    $focusElement.val(''); // Clear any existing content
                    
                    // Use setTimeout to ensure focus happens after current event loop
                    setTimeout(() => {
                        $focusElement.focus();
                        // Place cursor at start
                        $focusElement[0].setSelectionRange(0, 0);
                    }, 0);
                } else {
                    $focusElement.focus();
                }
            }
        }
    }

    static bindCardInteractions() {
        // Add tabindex to all cards
        $('.card').attr('tabindex', '0');

        // Add hover to focus behavior
        $(document).on('mouseenter', '.card', function() {
            $(this).focus();
        });

        $(document).on('mouseleave', '.card', function() {
            $(this).blur();
        });
    }
}
