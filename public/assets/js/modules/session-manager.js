class SessionManager {
    static init() {
        this.checkExistingSession();
    }

    static checkExistingSession() {
        $.get('/api/check-session.php')
            .done(response => {
                if (response.success && response.data.hasExistingSession && response.data.paymentVerified) {
                    this.showSessionRestoreOverlay();
                } else {
                    $.post('/api/clear-session.php')
                        .done(() => {
                            console.log('Session cleared successfully');
                        })
                        .fail(() => {
                            console.error('Failed to clear session');
                        });
                    $('.step').removeClass('active');
                    $('.step-introduction').addClass('active');
                }
            });
    }

    static showSessionRestoreOverlay() {
        $('#session-restore-overlay').fadeIn();
        this.bindRestoreActions();
    }

    static bindRestoreActions() {
        $('#restore-session').on('click', async () => {
            // Trigger event 'stepForwarded' with current step number
            const detail = { currentStep: 11 };
            const event = new CustomEvent('stepForwarded', { detail });
            document.dispatchEvent(event);

            try {
                const $overlay = $('#session-restore-overlay');
                // Hide overlay immediately
                $overlay.fadeOut();
                
                const response = await $.get('/api/check-session.php');
                
                if (response.success && response.data.version) {
                    const currentVersionNumber = response.data.version;
                    
                    // Show loading state for current version
                    $('.step').removeClass('active');
                    $(`.step-content-${currentVersionNumber}`).addClass('active');
                    $(`.step-content-${currentVersionNumber} .loading-indicator`).addClass('visible').find(`.loading-text h4`).text('Retrieving your session');
                    
                    // Load all versions up to current version
                    const versions = await this.loadAllVersions(currentVersionNumber);
                    console.log('Loaded versions:', versions);
                    
                    if (versions) {
                        // Update UI for each version
                        await this.updateUIForVersions(versions, currentVersionNumber);
                    } else {
                        throw new Error('Failed to load content versions');
                    }
                }
                
            } catch (error) {
                console.error('Session restore error:', error);
                Toast.show('Error restoring session. Starting fresh...', 'error');
                
                await $.post('/api/clear-session.php');
                $('.step').removeClass('active');
                $('.step-introduction').addClass('active');
            }
        });

        $('#start-new-session').on('click', () => {
            // Trigger event 'stepForwarded' with step number 11
            const detail = { currentStep: 1 };
            const event = new CustomEvent('stepForwarded', { detail });
            document.dispatchEvent(event);

            $.post('/api/clear-session.php')
                .done(() => {
                    $('.step').removeClass('active');
                    $('.step-introduction').addClass('active');
                    $('#session-restore-overlay').fadeOut();
                });
        });
    }

    static async loadAllVersions(currentVersion) {
        try {
            const promises = [];
            // Load all versions up to current version
            for (let i = 1; i <= currentVersion; i++) {
                promises.push($.get(`/api/get-full-content.php?version=${i}`));
            }
            
            const results = await Promise.all(promises);
            return results.filter(r => r.success);
        } catch (error) {
            console.error('Error loading versions:', error);
            return null;
        }
    }

    static async updateUIForVersions(versions, currentVersion) {
        for (let i = 1; i <= currentVersion; i++) {
            UIManager.updateContentStep(i, versions[i - 1].fullContent, true);
        }
    }

    static async getSessionVersion() {
        try {
            const response = await $.get('/api/check-session.php');
            if (response.success && response.data.version) {
                return response.data.version;
            }
            return false;
        } catch (error) {
            console.error('Error checking session version:', error);
            return false;
        }
    }
}
