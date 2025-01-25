class SectionManager {
    static init() {
        this.bindSectionTransitions();
    }

    static bindSectionTransitions() {
        $('.er-button, .card').on('click', function() {
            const classList = $(this).attr('class').split(/\s+/);
            let steps = 0;

            classList.forEach(function(className) {
                const match = className.match(/^go-next-(\d+)$/);
                if (match) {
                    steps = parseInt(match[1], 10);
                }
            });

            if (steps > 0) {
                SectionManager.showNextSection(steps);
            }
        });
    }

    static showNextSection(steps = 1) {
        const $currentSection = $('section.active');
        let $targetSection = $currentSection;

        for (let i = 0; i < steps; i++) {
            $targetSection = $targetSection.next('section');
            if (!$targetSection.length) return;
        }
        
        $currentSection.removeClass('active');
        $targetSection.addClass('active');
        
        // Trigger custom event for section change
        $targetSection.trigger('sectionActive');
        
        Accessibility.focusFirstElement($targetSection[0]);
    }
}