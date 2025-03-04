/**
 * Contact Form Handler
 * Handles the AJAX submission for the contractor contact form
 */
class ContactForm {
    constructor() {
        this.$formElement = $('#contractor-contact-form');
        this.$successContainer = $('#success-container');
        this.$formContainer = $('#contact-form-container');
        this.$messagesContainer = $('#form-messages');
        this.$spinner = $('.spinner');
        this.$submitButton = this.$formElement.find('button[type="submit"]');
        this.$resetButton = $('#reset-form-btn');
        
        this.bindEvents();
    }
    
    bindEvents() {
        if (this.$formElement.length) {
            this.$formElement.on('submit', this.handleSubmit.bind(this));
        }
        
        if (this.$resetButton.length) {
            this.$resetButton.on('click', this.resetForm.bind(this));
        }
    }
    
    validateForm() {
        const name = $('#name').val().trim();
        const email = $('#email').val().trim();
        const message = $('#message').val().trim();
        
        // Basic validation
        if (!name || !email || !message) {
            this.showError('Please fill in all required fields');
            return false;
        }
        
        // Email validation
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            this.showError('Please enter a valid email address');
            return false;
        }
        
        return true;
    }
    
    handleSubmit(e) {
        e.preventDefault();
        
        // Clear previous messages
        this.clearMessages();
        
        // Validate the form
        if (!this.validateForm()) {
            return false;
        }
        
        // Show loading state
        this.setLoading(true);
        
        // Collect form data
        const formData = {
            name: $('#name').val().trim(),
            email: $('#email').val().trim(),
            phone: $('#phone').val().trim(),
            company: $('#company').val().trim(),
            message: $('#message').val().trim()
        };

        // Submit form via AJAX
        HttpService.post('/api/submit-contact.php', formData)
            .then(response => {
                this.setLoading(false);
                
                if (response.success) {
                    this.showSuccess();
                } else {
                    this.showError(response.error || 'An error occurred while submitting the form.');
                }
            })
            .catch(error => {
                this.setLoading(false);
                this.showError('An unexpected error occurred. Please try again later.');
                console.error('Form submission error:', error);
            });
    }
    
    showSuccess() {
        this.$formContainer.hide();
        this.$successContainer.show();
        // Scroll to top of messages
        $('html, body').animate({ scrollTop: this.$messagesContainer.offset().top - 100 }, 'smooth');
    }
    
    showError(message) {
        this.$messagesContainer.text(message);
        this.$messagesContainer.attr('class', 'alert alert-error');
        this.$messagesContainer.show();
        // Scroll to error message
        $('html, body').animate({ scrollTop: this.$messagesContainer.offset().top - 100 }, 'smooth');
    }
    
    clearMessages() {
        this.$messagesContainer.text('');
        this.$messagesContainer.hide();
    }
    
    setLoading(isLoading) {
        if (isLoading) {
            this.$spinner.show();
            this.$submitButton.prop('disabled', true);
        } else {
            this.$spinner.hide();
            this.$submitButton.prop('disabled', false);
        }
    }
    
    resetForm() {
        this.$formElement[0].reset();
        this.$formContainer.show();
        this.$successContainer.hide();
        this.clearMessages();
    }
}

// Initialize the form handler when the DOM is fully loaded
$(document).ready(() => {
    // Initialize HttpService
    HttpService.init();
    
    // Initialize the contact form
    new ContactForm();
    
    SlideMenu.init();
});
