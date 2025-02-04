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

                // trigger our custom event 'stepForwarded' with the current step number, this event will be listened by progress bar
                const currentStepNumber = $('.step').index($currentStep) + 2;
                const detail = { currentStep: currentStepNumber };
                const event = new CustomEvent('stepForwarded', { detail });
                document.dispatchEvent(event);

            }
        });
    }
}
