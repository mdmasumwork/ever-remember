class ValidationService {
    static validateStep($step) {
        // If step is not required, return true
        if (!$step.hasClass('required')) {
            return true;
        }

        // Special case for terms agreement step
        if ($step.hasClass('step-terms-agreement')) {
            return this.validateTermsAgreement($step);
        }

        const $input = $step.find('input, textarea').first();
        if (!$input.length) {
            return true;
        }

        // Check for specific validation types based on step classes
        if ($step.hasClass('required-email')) {
            return this.validateEmail($input);
        } else if ($step.hasClass('required-name')) {
            return this.validateName($input);
        } else {
            // Default validation for required fields
            return this.validateRequired($input);
        }
    }

    static validateRequired($field) {
        const value = $field.val().trim();
        
        if (!value) {
            UIManager.showFieldError($field, 'This field is required');
            return false;
        }

        UIManager.clearFieldError($field);
        return true;
    }

    static validateName($field) {
        const value = $field.val().trim();
        const nameRegex = /^[a-zA-Z\s-]{2,50}$/;
        
        if (!value) {
            UIManager.showFieldError($field, 'This field is required');
            return false;
        }
        
        if (!nameRegex.test(value)) {
            UIManager.showFieldError($field, 'Please enter a valid name (2-50 letters, spaces, or hyphens)');
            return false;
        }

        UIManager.clearFieldError($field);
        return true;
    }

    static validateEmail($field) {
        const value = $field.val().trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (!value) {
            UIManager.showFieldError($field, 'This field is required');
            return false;
        }
        
        if (!emailRegex.test(value)) {
            UIManager.showFieldError($field, 'Please enter a valid email address');
            return false;
        }

        UIManager.clearFieldError($field);
        return true;
    }
    
    static validateTermsAgreement($step) {
        const $checkbox = $step.find('#terms-checkbox');
        
        if (!$checkbox.is(':checked')) {
            UIManager.showFieldError($checkbox.parent(), 'You must agree to the Terms of Service and Privacy Policy');
            return false;
        }
        
        UIManager.clearFieldError($checkbox.parent());
        return true;
    }
}
