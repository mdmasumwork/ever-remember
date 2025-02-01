class StepManager {
    static init() {
        this.bindstepTransitions();
    }

    static bindstepTransitions() {
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
                const $currentstep = $('.step.active');
                if (isSkipButton || StepManager.validateCurrentStep($currentstep)) {
                    StepManager.showNextStep(steps);
                }
            }
        });
    }

    static validateCurrentStep($step) {
        if ($step.hasClass('step-introduction')) {
            const $nameField = $('#first-person-name-field');
            return FormValidator.validateName($nameField);
        } else if ($step.hasClass('step-email')) {
            const $emailField = $('#email-field');
            return FormValidator.validateEmail($emailField);
        } else if ($step.hasClass('step-deceased-person-name')) {
            const $deceasedPersonNameField = $('#deceased-person-name-field');
            return FormValidator.validateName($deceasedPersonNameField);
        } else if ($step.hasClass('step-deceased-person-relation')) {
            const $deceasedPersonRelationField = $('#deceased-person-relation-field');
            return FormValidator.validateRelation($deceasedPersonRelationField);
        } else if ($step.hasClass('step-deceased-person-details')) {
            const $deceasedPersonDetailsField = $('#deceased-person-details-field');
            return FormValidator.validateDetails($deceasedPersonDetailsField);
        } else if ($step.hasClass('step-deceased-person-accomplishment')) {
            const $deceasedPersonAccomplishmentField = $('#deceased-person-accomplishment-field');
            return FormValidator.validateDetails($deceasedPersonAccomplishmentField);
        } else if ($step.hasClass('step-final-question')) {
            const $finalQuestionField = $('#final-question-field');
            return FormValidator.validateDetails($finalQuestionField);
        }
        return true;
    }

    static showNextStep(steps = 1) {
        const $currentstep = $('.step.active');
        DataManager.collectData($currentstep[0]);

        let $targetstep = $currentstep;
        for (let i = 0; i < steps; i++) {
            $targetstep = $targetstep.next('.step');
            if (!$targetstep.length) return;
        }
        
        $currentstep.removeClass('active');
        $targetstep.addClass('active');
        
        // Trigger custom event for step change
        $targetstep.trigger('stepActive');
        
        Accessibility.focusFirstElement($targetstep[0]);
    }
}