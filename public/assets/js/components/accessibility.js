class Accessibility {
    static init() {
        this.bindSectionEvents();
        this.bindKeyboardNavigation();
        this.initFirstSectionFocus();
    }

    static initFirstSectionFocus() {
        // Focus first element in active section on page load
        const $activeSection = $('section.active');
        if ($activeSection.length) {
            this.focusFirstElement($activeSection[0]);
        }
    }

    static bindSectionEvents() {
        // Listen for section activation
        $('section').on('sectionActive', function() {
            Accessibility.focusFirstElement(this);
        });
    }

    static focusFirstElement(section) {
        const $section = $(section);
        const sectionId = $section.attr('id') || $section.attr('class');

        // Special handling for message-tone section
        if (sectionId.includes('message-tone')) {
            const $firstCard = $section.find('.card').first();
            if ($firstCard.length) {
                $firstCard.focus();
                return;
            }
        }

        const $input = $section.find('input, textarea').first();
        if ($input.length) {
            $input.focus();
        } else {
            const $primaryButton = $section.find('.er-button.primary-button').first();
            if ($primaryButton.length) {
                $primaryButton.focus();
            } else {
                const $firstCard = $section.find('.card[tabindex="0"]');
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