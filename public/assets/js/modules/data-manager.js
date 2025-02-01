class DataManager {
    // TODO: Add input validation
    // TODO: Add CSRF protection
    // TODO: Add data sanitization
    // TODO: Add secure transmission
    static formData = {
        firstPersonName: '',
        email: '',
        deceasedPersonName: '',
        messageType: '',
        relationship: '',
        details: '',
        accomplishments: '',
        tone: '',
        finalQuestion: '',
        additionalInstructions: []
    };

    static collectData(step) {
        const $step = $(step);
        const stepId = $step.attr('id') || $step.attr('class');
        const clickedButton = document.activeElement;
        const isSkipButtonClicked = clickedButton && 
            (clickedButton.classList.contains('secondary-button') || 
             clickedButton.classList.contains('tertiary-button'));

        switch(true) {
            case stepId.includes('step-introduction'):
                this.formData.firstPersonName = $step.find('#first-person-name-field').val();
                break;

            case stepId.includes('step-email'):
                this.formData.email = $step.find('#email-field').val();
                break;

            case stepId.includes('deceased-person-name'):
                this.formData.deceasedPersonName = $step.find('#deceased-person-name-field').val();
                break;

            case stepId.includes('message-type'):
                this.formData.messageType = $step.find('.card.selected').data('message-type');
                break;

            case stepId.includes('deceased-person-relation'):
                this.formData.relationship = $step.find('#deceased-person-relation-field').val();
                break;

            case stepId.includes('deceased-person-details'):
                this.formData.details = $step.find('#deceased-person-details-field').val();
                break;

            case stepId.includes('deceased-person-accomplishment'):
                if (!isSkipButtonClicked) {
                    this.formData.accomplishments = $step.find('#deceased-person-accomplishment-field').val();
                } else {
                    this.formData.accomplishments = ''; // Clear any existing value
                }
                break;

            case stepId.includes('message-tone'):
                this.formData.tone = $step.find('.card.selected').data('message-tone');
                break;

            case stepId.includes('final-question'):
                if (!isSkipButtonClicked) {
                    this.formData.finalQuestion = $step.find('#final-question-field').val();
                } else {
                    this.formData.finalQuestion = ''; // Clear any existing value
                }
                this.sendToContentGeneration();
                break;

            case stepId.includes('step-additional-question-1'):
                if (!isSkipButtonClicked) {
                    const additionalInstruction_1 = $step.find('#additional-question-1-field').val();
                    this.formData.additionalInstructions.push(additionalInstruction_1);
                } else {
                    this.formData.additionalInstructions.push(''); // Push empty string when skipped
                }
                this.sendToContentGeneration(true);
                break;

            case stepId.includes('step-additional-question-2'):
                if (!isSkipButtonClicked) {
                    const additionalInstruction_2 = $step.find('#additional-question-2-field').val();
                    this.formData.additionalInstructions.push(additionalInstruction_2);
                } else {
                    this.formData.additionalInstructions.push(''); // Push empty string when skipped
                }
                this.sendToContentGeneration(true);
                break;

            case stepId.includes('step-feedback-2'):
                    const feedback2 = $step.find('#feedback-field-2').val();
                    this.sendFeedback(feedback2);
                    break;

            case stepId.includes('step-feedback'):
                if (!isSkipButtonClicked) {
                    const feedback = $step.find('#feedback-field').val();
                    this.sendFeedback(feedback);
                }
                break;
        
        }

        console.log('Collected data:', this.formData);
    }

    static async sendToContentGeneration(hasAdditionalInstruction = false) {
        try {
            $('.content-box').addClass('loading');
            $('.loading-indicator').show();

            const payload = hasAdditionalInstruction 
                ? { 
                    additionalInstruction: this.formData.additionalInstructions.slice(-1)[0],
                    isFirstRequest: false
                  } 
                : { 
                    ...this.formData,
                    isFirstRequest: true
                  };

            const response = await fetch('/api/generate-content.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.error);
            }

            if (data.version === 1) {
                UIManager.updateContentStep(data.preview, data.version);
            } else {
                UIManager.updateContentStep(data.fullContent, data.version, true);
            }
            
        } catch (error) {
            console.error('Content generation failed:', error);
            $('.loading-indicator').hide();
            $('.error-message').text(error.message).show();
        }
    }

    static async sendFeedback(feedback) {
        try {
            const response = await fetch('/api/store-feedback.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ feedback })
            });

            const data = await response.json();
            if (!data.success) {
                throw new Error(data.error);
            }
        } catch (error) {
            console.error('Failed to store feedback:', error);
            // Optionally show error to user
        }
    }

    static async getFullContent() {
        try {
            const response = await fetch('/api/get-full-content.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' }
            });

            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.error);
            }

            UIManager.updateContentStep(data.fullContent, data.version, true);
            
        } catch (error) {
            console.error('Failed to get full content:', error);
            $('#card-errors').text('Failed to retrieve content. Please try again.').show();
        }
    }
}