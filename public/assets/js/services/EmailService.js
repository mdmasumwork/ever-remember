class EmailService {
    static init() {
        $(document).on('click', '.email-icon', function() {
            const tabPane = $(this).closest('.tab-pane');
            let version = 1;
            if (tabPane.hasClass('version-2')) {
                version = 2;
            }
            Toast.show('Sending email...');
            // Just send the version number
            EmailService.sendEmail(version);
        });
    }

    static async sendEmail(version) {
        try {
            const response = await HttpService.post('/api/send-email.php', { version });
            if (!response.success) {
                throw new Error(response.message);
            }
            Toast.show('Email sent successfully!');
        } catch (error) {
            console.error('Failed to send email:', error);
            alert('Failed to send email: ' + error.message);
        }
    }
}
