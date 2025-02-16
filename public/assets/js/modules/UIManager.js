class UIManager {

    static updatePaymentUI(event) {
        if (event === 'payment-initiated') {
            $('.payment-overlay .initial-button').hide();
            $('.payment-form').slideDown();
        } else if (event === 'payment-success') {
            $('.payment-overlay').removeClass('visible');
            $('.payment-success-overlay').addClass('visible');
        } else if (event === 'continue-after-payment') {
            console.log('continue-after-payment');
            $('.step-content-1 .content-actions').removeClass('visible').find('.payment-success-overlay').removeClass('visible');
            $('.step-content-1 .content-box').removeClass('masked').removeClass('visible');            ;
            $('.step-content-1 .loading-indicator').addClass('visible').find('.loading-text h4').text('Retrieving full content');            
        }
    }


    static updateContentStep(version, content, isPaid = false) {

        $(`.tab-pane.version-${version} .loading-indicator`).removeClass('visible');
        $(`.tab-pane.version-${version} .content-box`).addClass('visible').find('.generated-content').html(content);
        $(`.step-content-${version} .content-actions`).addClass('visible');

        if (isPaid) {
            $(`.tab-pane.version-${version} .content-box`).removeClass('masked').addClass('visible');
            $(`.tab-pane.version-${version} .content-copy-actions`).addClass('visible');
            $(`.step-content-${version} .content-action`).addClass('visible');
            $(`.step-content-${version} .payment-overlay`).removeClass('visible');
        } else {
            $(`.tab-pane.version-${version} .content-box`).removeClass('visible').addClass('masked');
            $(`.step-content-${version} .content-action`).removeClass('visible');
            $(`.step-content-${version} .payment-overlay`).addClass('visible');
        }
        
    }

    static showNextStep(steps = 1) {
        const $currentStep = $('.step.active');

        // Find target step
        let $targetStep = $currentStep;
        for (let i = 0; i < steps; i++) {
            $targetStep = $targetStep.next('.step');
            if (!$targetStep.length) return;
        }

        // Perform transition
        $currentStep.removeClass('active');
        $targetStep.addClass('active');
        
        // Call focus after transition
        Accessibility.focusActiveStepElement();
    }

    static showPreviousStep(steps = 1) {
        const $currentStep = $('.step.active');

        // Find target step
        let $targetStep = $currentStep;
        for (let i = 0; i < steps; i++) {
            $targetStep = $targetStep.prev('.step');
            if (!$targetStep.length) return;
        }

        // Perform transition
        $currentStep.removeClass('active');
        $targetStep.addClass('active');
        
        // Call focus after transition
        Accessibility.focusActiveStepElement();
    }

    static showFieldError($field, message) {
        // First clear any existing error state
        this.clearFieldError($field);
        
        // Add error classes
        $field.addClass('error');
        const $fieldArea = $field.closest('.field-area');
        $fieldArea.addClass('error');
        
        // Show error message
        const $step = $field.closest('.step');
        $step.find('.field-error-area').html(`<div class="field-error">${message}</div>`);
        
        // Add shake animation
        $field.addClass('shake');
        setTimeout(() => $field.removeClass('shake'), 500);
        
        // Focus the field
        $field.focus();
    }

    static clearFieldError($field) {
        // Remove error classes
        $field.removeClass('error shake');
        const $fieldArea = $field.closest('.field-area');
        $fieldArea.removeClass('error');
        
        // Clear error message
        const $step = $field.closest('.step');
        $step.find('.field-error-area').empty();
    }
}