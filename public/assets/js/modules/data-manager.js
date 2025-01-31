class DataManager {
    // TODO: Add input validation
    // TODO: Add CSRF protection
    // TODO: Add data sanitization
    // TODO: Add secure transmission
    static formData = {
        firstPersonName: '',
        email: '',
        deadPersonName: '',
        messageType: '',
        relationship: '',
        details: '',
        accomplishments: '',
        tone: '',
        finalQuestion: '',
        additionalInstructions: []
    };

    static collectData(section) {
        const $section = $(section);
        const sectionId = $section.attr('id') || $section.attr('class');

        switch(true) {
            case sectionId.includes('section-introduction'):
                this.formData.firstPersonName = $section.find('#first-person-name-field').val();
                break;

            case sectionId.includes('section-email'):
                this.formData.email = $section.find('#email-field').val();
                break;

            case sectionId.includes('dead-person-name'):
                this.formData.deadPersonName = $section.find('#dead-person-name-field').val();
                break;

            case sectionId.includes('message-type'):
                this.formData.messageType = $section.find('.card.selected').data('message-type');
                break;

            case sectionId.includes('dead-person-relation'):
                this.formData.relationship = $section.find('#dead-person-relation-field').val();
                break;

            case sectionId.includes('dead-person-details'):
                this.formData.details = $section.find('#dead-person-details-field').val();
                break;

            case sectionId.includes('dead-person-accomplishment'):
                this.formData.accomplishments = $section.find('#dead-person-accomplishment-field').val();
                break;

            case sectionId.includes('message-tone'):
                this.formData.tone = $section.find('.card.selected').data('message-tone');
                break;

            case sectionId.includes('final-question'):
                this.formData.finalQuestion = $section.find('#final-question-field').val();
                this.sendToContentGeneration();
                break;

            case sectionId.includes('section-additional-question-1'):
                const additionalInstruction_1 = $section.find('#additional-question-1-field').val();
                this.formData.additionalInstructions.push(additionalInstruction_1);
                this.sendToContentGeneration(true);
                break;

            case sectionId.includes('section-additional-question-2'):
                const additionalInstruction_2 = $section.find('#additional-question-2-field').val();
                this.formData.additionalInstructions.push(additionalInstruction_2);
                this.sendToContentGeneration(true);
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

            // Update UI based on version
            // $('.loading-indicator').hide();
            // if (data.version === 1) {
            //     $(`.version-${data.version} .generated-content`)
            //         .html(data.preview)
            //         .addClass('visible');                
            //     // $('.payment-overlay').show();
            // } else {
            //     $(`.version-${data.version} .generated-content`)
            //         .html(data.fullContent)
            //         .addClass('visible');
            //     $(`.section-content-${data.version} .tab-pane.version-${data.version} .content-copy-actions`).addClass('visible');
            // }
            // $(`.section-content-${data.version} .content-actions`).addClass('visible');

            if (data.version === 1) {
                UIManager.updateContentSection(data.preview, data.version);
            } else {
                UIManager.updateContentSection(data.fullContent, data.version, true);
            }
            
        } catch (error) {
            console.error('Content generation failed:', error);
            $('.loading-indicator').hide();
            $('.error-message').text(error.message).show();
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

            UIManager.updateContentSection(data.fullContent, data.version, true);
            
        } catch (error) {
            console.error('Failed to get full content:', error);
            $('#card-errors').text('Failed to retrieve content. Please try again.').show();
        }
    }
}