class StepManager {
    static init() {
        this.bindStepForwardListener();
    }

    static bindStepForwardListener() {
        $(document).on('stepForwardRequested', (event, steps, isSkipButton) => {
            const $currentStep = $('.step.active');
            if (FormValidator.validateStep($currentStep)) {
                DataManager.collectData($currentStep[0], isSkipButton);
                UIManager.showNextStep(steps);
            }
        });
    }
}
