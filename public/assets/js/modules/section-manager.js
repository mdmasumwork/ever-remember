class SectionManager {
    static init() {
        this.bindSectionTransitions();
    }

    static bindSectionTransitions() {
        $('.er-button, .card').on('click', function() {
            const classList = $(this).attr('class').split(/\s+/);
            let steps = 0;
            const isSkipButton = $(this).hasClass('secondary-button');

            classList.forEach(function(className) {
                const match = className.match(/^go-next-(\d+)$/);
                if (match) {
                    steps = parseInt(match[1], 10);
                }
            });

            if (steps > 0) {
                const $currentSection = $('section.active');
                if (isSkipButton || SectionManager.validateCurrentSection($currentSection)) {
                    SectionManager.showNextSection(steps);
                }
            }
        });
    }

    static validateCurrentSection($section) {
        if ($section.hasClass('section-introduction')) {
            const $nameField = $('#first-person-name-field');
            return FormValidator.validateName($nameField);
        } else if ($section.hasClass('section-email')) {
            const $emailField = $('#email-field');
            return FormValidator.validateEmail($emailField);
        } else if ($section.hasClass('section-deceased-person-name')) {
            const $deceasedPersonNameField = $('#deceased-person-name-field');
            return FormValidator.validateName($deceasedPersonNameField);
        } else if ($section.hasClass('section-deceased-person-relation')) {
            const $deceasedPersonRelationField = $('#deceased-person-relation-field');
            return FormValidator.validateRelation($deceasedPersonRelationField);
        } else if ($section.hasClass('section-deceased-person-details')) {
            const $deceasedPersonDetailsField = $('#deceased-person-details-field');
            return FormValidator.validateDetails($deceasedPersonDetailsField);
        } else if ($section.hasClass('section-deceased-person-accomplishment')) {
            const $deceasedPersonAccomplishmentField = $('#deceased-person-accomplishment-field');
            return FormValidator.validateDetails($deceasedPersonAccomplishmentField);
        } else if ($section.hasClass('section-final-question')) {
            const $finalQuestionField = $('#final-question-field');
            return FormValidator.validateDetails($finalQuestionField);
        }
        return true;
    }

    static showNextSection(steps = 1) {
        const $currentSection = $('section.active');
        DataManager.collectData($currentSection[0]);

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