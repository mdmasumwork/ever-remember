class DataManager {
    // TODO: Add input validation
    // TODO: Add CSRF protection
    // TODO: Add data sanitization
    // TODO: Add secure transmission
    // static formData = {
    //     firstPersonName: 'Md Masum',
    //     email: 'md.masum.dev@gmail.com',
    //     deceasedPersonName: 'Nanet',
    //     messageType: 'eulogy',
    //     relationship: 'neighbor',
    //     details: 'nice person',
    //     accomplishments: 'teacher',
    //     messageTone: 'formal',
    //     finalQuestion: '',
    //     additionalInstructions: []
    // };

    static formData = {
        firstPersonName: '',
        email: '',
        deceasedPersonName: '',
        messageType: '',
        relationship: '',
        details: '',
        accomplishments: '',
        messageTone: '',
        finalQuestion: '',
        additionalInstructions: []
    };

    static collectData(step, toSkip = false) {
        const $step = $(step);
        const stepId = $step.attr('id') || $step.attr('class');

        switch(true) {
            case stepId.includes('step-introduction'):
                if (!toSkip) {
                    this.formData.firstPersonName = $step.find('#first-person-name-field').val().split(' ').map(name => name.trim().charAt(0).toUpperCase() + name.trim().slice(1)).join(' ');
                    $('.first-person-name-placeholder').text(this.formData.firstPersonName);
                }
                break;

            case stepId.includes('step-email'):
                if (!toSkip) {
                    this.formData.email = $step.find('#email-field').val();
                }
                break;

            case stepId.includes('deceased-person-name'):
                if (!toSkip) {
                    this.formData.deceasedPersonName = $step.find('#deceased-person-name-field').val().split(' ').map(name => name.trim().charAt(0).toUpperCase() + name.trim().slice(1)).join(' ');
                    $('.deceased-person-name-placeholder').text(this.formData.deceasedPersonName);
                }
                break;

            case stepId.includes('message-type'):
                if (!toSkip) {
                    this.formData.messageType = $step.find('.card.selected').data('message-type');
                    $('.message-type-placeholder').text(this.formData.messageType);
                }
                break;

            case stepId.includes('deceased-person-relation'):
                if (!toSkip) {
                    this.formData.relationship = $step.find('#deceased-person-relation-field').val();
                }
                break;

            case stepId.includes('deceased-person-details'):
                if (!toSkip) {
                    this.formData.details = $step.find('#deceased-person-details-field').val();
                }
                break;

            case stepId.includes('deceased-person-accomplishment'):
                if (!toSkip && $step.find('#deceased-person-accomplishment-field').val().length) {
                    this.formData.accomplishments = $step.find('#deceased-person-accomplishment-field').val();
                } else {
                    this.formData.accomplishments = 'I am not sure what to say.';
                }
                break;

            case stepId.includes('message-tone'):
                if (!toSkip) {
                    this.formData.messageTone = $step.find('.card.selected').data('message-tone');
                }
                break;

            case stepId.includes('final-question'):
                if (!toSkip && $step.find('#final-question-field').val().length) {
                    this.formData.finalQuestion = $step.find('#final-question-field').val();
                } else {
                    this.formData.finalQuestion = 'No, I have noting more to add.';
                }
                this.sendToContentGeneration();
                break;

            case stepId.includes('step-additional-question-1'):
                let additionalInstruction_1 = 'No, I have noting more to add.';
                if (!toSkip) {
                    additionalInstruction_1 = $step.find('#additional-question-1-field').val();                    
                }
                this.formData.additionalInstructions.push(additionalInstruction_1);
                this.sendToContentGeneration(true);
                break;

            case stepId.includes('step-additional-question-2'):
                let additionalInstruction_2 = 'No, I have noting more to add.';
                if (!toSkip) {
                    additionalInstruction_2 = $step.find('#additional-question-2-field').val();                    
                }
                this.formData.additionalInstructions.push(additionalInstruction_2);
                this.sendToContentGeneration(true);
                break;

            case stepId.includes('step-feedback-2'):
                const feedback2 = $step.find('#feedback-field-2').val();
                if (feedback2.trim()) {
                    this.sendFeedback(feedback2);
                }
                break;

            case stepId.includes('step-feedback'):
                const feedback = $step.find('#feedback-field').val();
                if (feedback.trim()) {
                    this.sendFeedback(feedback);
                }
                break;
        }
    }

    static async sendToContentGeneration(hasAdditionalInstruction = false) {
        try {
            const payload = hasAdditionalInstruction 
                ? { additionalInstruction: this.formData.additionalInstructions.slice(-1)[0] } 
                : this.formData;
            
            const data = await HttpService.post('/api/generate-content.php', payload);
            
            if (!data.success) {
                throw new Error(data.error);
            }

            if (data.version === 1) {
                UIManager.updateContentStep(data.version, data.preview);
            } else {
                UIManager.updateContentStep(data.version, data.fullContent, true);
            }
            
        } catch (error) {
            console.error('Content generation failed:', error);
            $('.loading-indicator').removeClass('visible');
            $('.error-message').text(error.message).show();
        }
    }

    static async sendFeedback(feedback) {
        try {
            const data = await HttpService.post('/api/store-feedback.php', { feedback });
            
            if (!data.success) {
                throw new Error(data.error);
            }
        } catch (error) {
            console.error('Failed to store feedback:', error);
            // Optionally show error to user
        }
    }
}