class ProgressBar {
    static init() {
        this.$steps = $('.vertical-progress__step');
        this.$steps.first().addClass('active');
        this.bindEvents();
    }

    static bindEvents() {
        $(document).on('stepForwarded', (event) => {
            const currentStep = event.detail.currentStep;
            this.updateProgress(currentStep < 11 ? currentStep : 11);
        });
    }

    static updateProgress(currentStep) {
        this.$steps.each(function(index) {
            const stepNumber = index + 1;
            const $step = $(this);
            
            $step.removeClass('active');
            
            if (stepNumber < currentStep) {
                $step.addClass('completed').removeClass('active');
            } else if (stepNumber === currentStep) {
                $step.addClass('active').removeClass('completed');
            } else {
                $step.removeClass('completed active');
            }
        });
    }
};
