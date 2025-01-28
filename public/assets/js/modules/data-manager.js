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
        finalQuestion: ''
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
        }

        console.log('Collected data:', this.formData);
    }

    static async sendToContentGeneration() {
        try {
            // Show loading state
            $('.loading-indicator').show();
            $('.content-box').addClass('loading');

            // First request - get preview
            const response = await fetch('/api/generate-content.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(this.formData)
            });

            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.error);
            }

            // Update UI with preview
            $('.loading-indicator').hide();
            $('.generated-content')
                .html(data.preview)
                .show();
            $('.payment-overlay').show();
            $('.content-box').removeClass('loading').addClass('masked');
            
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

            // Update UI with full content
            $('.generated-content')
                .html(data.fullContent)
                .show();
            $('.payment-overlay').hide();
            $('.content-box').removeClass('masked');
            
        } catch (error) {
            console.error('Failed to get full content:', error);
            $('#card-errors').text('Failed to retrieve content. Please try again.').show();
        }
    }
}