class StepManager {
    static init() {
        this.bindStepForwardListener();
        this.bindTermsCheckboxListener();
    }

    static bindStepForwardListener() {
        $(document).on('stepForwardRequested', (event, steps, isSkipButton) => {
            const $currentStep = $('.step.active');
            if (ValidationService.validateStep($currentStep)) {
                DataManager.collectData($currentStep[0], isSkipButton);
                UIManager.showNextStep(steps);

                // trigger our custom event 'stepForwarded' with the current step number, this event will be listened by progress bar
                const currentStepNumber = $('.step').index($currentStep) + 2;
                const detail = { currentStep: currentStepNumber };
                const event = new CustomEvent('stepForwarded', { detail });
                document.dispatchEvent(event);
            }
        });
    }
    
    static bindTermsCheckboxListener() {
        $(document).on('change', '#terms-checkbox', function() {
            const $agreeButton = $('#terms-agree-button');
            if (this.checked) {
                $agreeButton.removeClass('disabled');
                $agreeButton.prop('disabled', false);
            } else {
                $agreeButton.addClass('disabled');
                $agreeButton.prop('disabled', true);
            }
        });
    }
}
