class DataManager {

    static formData = {
        firstPersonName: '',
        email: '',
        deceasedPersonName: '',
        messageType: '',
        relationship: '',
        details: '',
        additionalInfo: '',
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
                    this.storeFormData('firstPersonName', this.formData.firstPersonName);
                }
                break;

            case stepId.includes('step-email'):
                if (!toSkip) {
                    this.formData.email = $step.find('#email-field').val();
                    this.storeFormData('email', this.formData.email);
                }
                break;

            case stepId.includes('deceased-person-name'):
                if (!toSkip) {
                    this.formData.deceasedPersonName = $step.find('#deceased-person-name-field').val().split(' ').map(name => name.trim().charAt(0).toUpperCase() + name.trim().slice(1)).join(' ');
                    $('.deceased-person-name-placeholder').text(this.formData.deceasedPersonName);
                    this.storeFormData('deceasedPersonName', this.formData.deceasedPersonName);
                }
                break;

            case stepId.includes('message-type'):
                if (!toSkip) {
                    this.formData.messageType = $step.find('.card.selected').data('message-type');
                    $('.message-type-placeholder').text(this.formData.messageType);
                    this.storeFormData('messageType', this.formData.messageType);
                    this.fetchQuestionsForMessageType(this.formData.messageType);
                }
                break;

            case stepId.includes('deceased-person-relation'):
                if (!toSkip) {
                    this.formData.relationship = $step.find('#deceased-person-relation-field').val();
                    this.storeFormData('relationship', this.formData.relationship);
                    console.log('relationship:', this.formData);
                }
                break;

            case stepId.includes('deceased-person-details'):
                if (!toSkip) {
                    this.formData.details = $step.find('#deceased-person-details-field').val();
                    this.storeFormData('details', this.formData.details);
                }
                break;

            case stepId.includes('message-tone'):
                if (!toSkip) {
                    this.formData.messageTone = $step.find('.card.selected').data('message-tone');
                    this.storeFormData('messageTone', this.formData.messageTone)
                        .then(() => {
                            // Only fetch additional info question after message tone is stored
                            this.fetchAdditionalInfoQuestion();
                        })
                        .catch(error => {
                            console.error('Failed to store message tone:', error);
                        });
                }
                break;

            case stepId.includes('additional-info'):
                if (!toSkip && $step.find('#additional-info-field').val().length) {
                    this.formData.additionalInfo = $step.find('#additional-info-field').val();
                } else {
                    this.formData.additionalInfo = 'I am not sure what to say.';
                }
                
                this.storeFormData('additionalInfo', this.formData.additionalInfo);
                break;

            case stepId.includes('final-question'):
                if (!toSkip && $step.find('#final-question-field').val().length) {
                    this.formData.finalQuestion = $step.find('#final-question-field').val();
                } else {
                    this.formData.finalQuestion = 'No, I have noting more to add.';
                }
                
                this.storeFormData('finalQuestionAnswer', this.formData.finalQuestion)
                    .then(() => {
                        // Send data to content generation after final question is stored
                        this.sendToContentGeneration();
                    })
                    .catch(error => {
                        console.error('Failed to store final question:', error);
                        // Still try to send data to content generation if storage fails
                        this.sendToContentGeneration();
                    });
                break;

            case stepId.includes('step-additional-question-1'):
                let additionalInstruction_1 = 'No, I have noting more to add.';
                if (!toSkip) {
                    additionalInstruction_1 = $step.find('#additional-question-1-field').val();                    
                }

                this.formData.additionalInstructions.push(additionalInstruction_1);
                this.storeFormData('additionalInstruction', additionalInstruction_1).
                    then(() => {
                        this.sendToContentGeneration(true);
                    })
                    .catch(error => {
                        console.error('Failed to store additional instruction:', error);
                        // Still try to send data to content generation if storage fails
                        this.sendToContentGeneration(true);
                    });

                break;

            case stepId.includes('step-additional-question-2'):
                let additionalInstruction_2 = 'No, I have noting more to add.';
                if (!toSkip) {
                    additionalInstruction_2 = $step.find('#additional-question-2-field').val();                    
                }
                this.formData.additionalInstructions.push(additionalInstruction_2);
                this.storeFormData('additionalInstruction', additionalInstruction_2).
                    then(() => {
                        this.sendToContentGeneration(true);
                    })
                    .catch(error => {
                        console.error('Failed to store additional instruction:', error);
                    });
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

    // New method to store form data in the backend session
    static async storeFormData(fieldName, value) {
        try {
            const data = await HttpService.post('/api/store-form-data.php', {
                field_name: fieldName,
                value: value
            });
            
            if (!data.success) {
                console.error('Failed to store form data:', data.error);
            }
        } catch (error) {
            console.error('Error storing form data:', error);
            // Silently fail, as this is a background operation
        }
    }

    static async fetchQuestionsForMessageType(messageType) {
        try {
            const response = await HttpService.get('/api/get-questions.php?message_type=' + messageType);
            
            if (!response.success) {
                throw new Error(response.error || 'Failed to fetch questions');
            }
            
            this.updateDeceasedPersonDetailsStep(response.questions);
        } catch (error) {
            console.error('Failed to fetch questions:', error);
            // Continue with default questions if there's an error
        }
    }
    
    static updateDeceasedPersonDetailsStep(questions) {
        const $step = $('.step-deceased-person-details');
        
        // Update the title
        $step.find('h4').text(questions.title);
        
        // Update the description
        $step.find('p').first().html(questions.description);
        
        // Update the suggestions list
        const $ul = $step.find('ul');
        $ul.empty();
        
        questions.suggestions.forEach(suggestion => {
            $ul.append(`<li>${suggestion}</li>`);
        });

        $('.deceased-person-details-field').attr('placeholder', questions.placeholder);
    }

    static async fetchAdditionalInfoQuestion() {
        try {
            const $additionalInfoStep = $('.step-additional-info');
            
            // Show loading indicator
            // $additionalInfoStep.find('.loading-indicator').addClass('visible');
            // $additionalInfoStep.find('.step-content').removeClass('visible');
            
            // Request the additional info question from the backend
            const response = await HttpService.get('/api/get-questions.php?step=additional-info');
            
            if (!response.success) {
                throw new Error(response.error || 'Failed to fetch additional info question');
            }
            
            // Process the response
            if (response.additionalInfoRequired === false) {
                // No additional info needed, skip this step
                console.log('No additional info needed, skipping step');
                $(document).trigger('stepForwardRequested', [1, true]);
                return;
            }
            
            // Update the step with the question
            if (response.question) {
                $additionalInfoStep.find('h4').html(response.question);                
            }

            $additionalInfoStep.find('.loading-indicator').hide();
            $additionalInfoStep.find('.step-content').addClass('visible');
            
        } catch (error) {
            console.error('Failed to fetch additional info question:', error);
            // On error, show the step with default question
            const $additionalInfoStep = $('.step-additional-info');
            $additionalInfoStep.find('.loading-indicator').removeClass('visible');
            $additionalInfoStep.find('.step-content').addClass('visible');
        }
    }

    static async sendToContentGeneration(hasAdditionalInstruction = false) {
        try {
            // const payload = hasAdditionalInstruction 
            //     ? { additionalInstruction: this.formData.additionalInstructions.slice(-1)[0] } 
            //     : this.formData;
            
            // const data = await HttpService.post('/api/generate-content.php', payload);
            const data = await HttpService.post('/api/generate-content.php');
            
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