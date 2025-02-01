class FormValidator {
    static init() {
        this.bindFirstPersonNameValidation();
        this.bindEmailValidation();
        this.bindDeceasedPersonNameValidation();
        this.bindDeceasedPersonRelationValidation();
        this.bindDeceasedPersonDetailsValidation();
        this.bindDeceasedPersonAccomplishmentValidation();
        this.bindFinalQuestionValidation();
        this.bindAdditionalQuestion1Validation();
        this.bindAdditionalQuestion2Validation();
        this.bindFeedbackValidation();
        this.bindFeedback2Validation();
    }

    static bindFirstPersonNameValidation() {
        const $nameField = $('#first-person-name-field');

        // Prevent form submission on Enter key if invalid
        $nameField.on('keypress', function(e) {
            if (e.which === 13 && !FormValidator.validateName($nameField)) {
                e.preventDefault();
                e.stopPropagation();
                $nameField.focus();
                $nameField.addClass('shake');
                setTimeout(() => $nameField.removeClass('shake'), 500);
            }
        });
    }

    static bindEmailValidation() {
        const $emailField = $('#email-field');

        // Prevent form submission on Enter key if invalid
        $emailField.on('keypress', function(e) {
            if (e.which === 13 && !FormValidator.validateEmail($emailField)) {
                e.preventDefault();
                e.stopPropagation();
                $emailField.focus();
                $emailField.addClass('shake');
                setTimeout(() => $emailField.removeClass('shake'), 500);
            }
        });
    }

    static bindDeceasedPersonNameValidation() {
        const $deceasedPersonNameField = $('#deceased-person-name-field');

        // Prevent form submission on Enter key if invalid
        $deceasedPersonNameField.on('keypress', function(e) {
            if (e.which === 13 && !FormValidator.validateName($deceasedPersonNameField)) {
                e.preventDefault();
                e.stopPropagation();
                $deceasedPersonNameField.focus();
                $deceasedPersonNameField.addClass('shake');
                setTimeout(() => $deceasedPersonNameField.removeClass('shake'), 500);
            }
        });
    }

    static bindDeceasedPersonRelationValidation() {
        const $deceasedPersonRelationField = $('#deceased-person-relation-field');

        // Prevent form submission on Enter key if invalid
        $deceasedPersonRelationField.on('keypress', function(e) {
            if (e.which === 13 && !FormValidator.validateRelation($deceasedPersonRelationField)) {
                e.preventDefault();
                e.stopPropagation();
                $deceasedPersonRelationField.focus();
                $deceasedPersonRelationField.addClass('shake');
                setTimeout(() => $deceasedPersonRelationField.removeClass('shake'), 500);
            }
        });
    }

    static bindDeceasedPersonDetailsValidation() {
        const $deceasedPersonDetailsField = $('#deceased-person-details-field');

        // Prevent form submission on Enter key if invalid
        $deceasedPersonDetailsField.on('keypress', function(e) {
            if (e.which === 13 && !FormValidator.validateDetails($deceasedPersonDetailsField)) {
                e.preventDefault();
                e.stopPropagation();
                $deceasedPersonDetailsField.focus();
                $deceasedPersonDetailsField.addClass('shake');
                setTimeout(() => $deceasedPersonDetailsField.removeClass('shake'), 500);
            }
        });
    }

    static bindDeceasedPersonAccomplishmentValidation() {
        const $deceasedPersonAccomplishmentField = $('#deceased-person-accomplishment-field');

        // Prevent form submission on Enter key if invalid
        $deceasedPersonAccomplishmentField.on('keypress', function(e) {
            if (e.which === 13 && !FormValidator.validateDetails($deceasedPersonAccomplishmentField)) {
                e.preventDefault();
                e.stopPropagation();
                $deceasedPersonAccomplishmentField.focus();
                $deceasedPersonAccomplishmentField.addClass('shake');
                setTimeout(() => $deceasedPersonAccomplishmentField.removeClass('shake'), 500);
            }
        });
    }

    static bindFinalQuestionValidation() {
        const $finalQuestionField = $('#final-question-field');

        // Prevent form submission on Enter key if invalid
        $finalQuestionField.on('keypress', function(e) {
            if (e.which === 13 && !FormValidator.validateDetails($finalQuestionField)) {
                e.preventDefault();
                e.stopPropagation();
                $finalQuestionField.focus();
                $finalQuestionField.addClass('shake');
                setTimeout(() => $finalQuestionField.removeClass('shake'), 500);
            }
        });
    }

    static bindAdditionalQuestion1Validation() {
        const $field = $('#additional-question-1-field');
        $field.on('keypress', function(e) {
            if (e.which === 13 && !FormValidator.validateDetails($field)) {
                e.preventDefault();
                e.stopPropagation();
                $field.focus();
                $field.addClass('shake');
                setTimeout(() => $field.removeClass('shake'), 500);
            }
        });
    }

    static bindAdditionalQuestion2Validation() {
        const $field = $('#additional-question-2-field');
        $field.on('keypress', function(e) {
            if (e.which === 13 && !FormValidator.validateDetails($field)) {
                e.preventDefault();
                e.stopPropagation();
                $field.focus();
                $field.addClass('shake');
                setTimeout(() => $field.removeClass('shake'), 500);
            }
        });
    }

    static bindFeedbackValidation() {
        const $field = $('#feedback-field');
        $field.on('keypress', function(e) {
            if (e.which === 13 && !FormValidator.validateFeedback($field)) {
                e.preventDefault();
                e.stopPropagation();
                $field.focus();
                $field.addClass('shake');
                setTimeout(() => $field.removeClass('shake'), 500);
            }
        });
    }

    static bindFeedback2Validation() {
        const $field = $('#feedback-field-2');
        $field.on('keypress', function(e) {
            if (e.which === 13 && !FormValidator.validateFeedback($field)) {
                e.preventDefault();
                e.stopPropagation();
                $field.focus();
                $field.addClass('shake');
                setTimeout(() => $field.removeClass('shake'), 500);
            }
        });
    }

    static validateName($field) {
        const value = $field.val().trim();
        const nameRegex = /^[a-zA-Z\s-]{2,50}$/;
        
        if (!value) {
            this.showError($field, 'This field is required');
            return false;
        }
        
        if (!nameRegex.test(value)) {
            this.showError($field, 'Please enter a valid name (2-50 letters, spaces, or hyphens)');
            return false;
        }

        this.removeError($field);
        return true;
    }

    static validateEmail($field) {
        const value = $field.val().trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (!value) {
            this.showError($field, 'This field is required');
            return false;
        }
        
        if (!emailRegex.test(value)) {
            this.showError($field, 'Please enter a valid email address');
            return false;
        }

        this.removeError($field);
        return true;
    }

    static validateRelation($field) {
        const value = $field.val().trim();
        
        if (!value) {
            this.showError($field, 'This field is required');
            return false;
        }

        this.removeError($field);
        return true;
    }

    static validateDetails($field) {
        const value = $field.val().trim();
        
        if (!value) {
            // Check if the field is an additional question field
            if ($field.attr('id').includes('additional-question')) {
                this.showError($field, "This field is required. Please either fill it in or click 'Skip'.");
            } else {
                this.showError($field, 'This field is required');
            }
            return false;
        }

        this.removeError($field);
        return true;
    }

    static validateFeedback($field) {
        const value = $field.val().trim();
        
        if (!value) {
            // Different message for feedback-2 since it has no "Maybe later" button
            if ($field.attr('id') === 'feedback-field-2') {
                this.showError($field, "Please provide your feedback to help us improve.");
            } else {
                this.showError($field, "This field is required. Please either provide feedback or click 'Maybe later'.");
            }
            return false;
        }

        this.removeError($field);
        return true;
    }

    static showError($field, message) {
        this.removeError($field);
        $field.addClass('error');
        const $fieldArea = $field.closest('.field-area');
        $fieldArea.addClass('error');
        // Find error area within the current step
        const $step = $field.closest('.step');
        $step.find('.field-error-area').html(`<div class="field-error">${message}</div>`);
    }

    static removeError($field) {
        $field.removeClass('error');
        const $fieldArea = $field.closest('.field-area');
        $fieldArea.removeClass('error');
        // Clear error only in the current step
        const $step = $field.closest('.step');
        $step.find('.field-error-area').empty();
    }
}