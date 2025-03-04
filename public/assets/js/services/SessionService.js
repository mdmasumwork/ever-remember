class SessionService {
    static init() {
        this.checkExistingSession();
        this.bindGoodbyeActions();
    }

    static async checkExistingSession() {
        try {
            const response = await HttpService.get('/api/check-session.php');
            if (response.success && response.data.version) {
                this.showSessionRestoreOverlay();
            } else {
                $('.step').removeClass('active');
                $('.step-introduction').addClass('active');
            }
        } catch (error) {
            console.error('Error checking existing session:', error);
        }
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
                
                const response = await HttpService.get('/api/check-session.php');
                
                if (response.success && response.data.version) {
                    const currentVersionNumber = response.data.version;

                    // Show loading state for current version
                    if (response.data.paymentVerified) {
                        $('.step').removeClass('active');
                        $(`.step-content-${currentVersionNumber}`).addClass('active');
                        $(`.step-content-${currentVersionNumber} .loading-indicator`).addClass('visible').find(`.loading-text h4`).text('Retrieving your session');                        
                    } else {
                        $('.step-content-1').addClass('active').find('.loading-indicator').addClass('visible').find('.loading-text h4').text('Retrieving your session');
                    }
                    
                    // Load all versions up to current version
                    const versions = await this.loadAllVersions(currentVersionNumber);

                    if (versions) {
                        if (response.data.paymentVerified) {
                            this.updateUIForVersions(versions, currentVersionNumber);
                        } else {
                            UIManager.updateContentStep(1, versions[0].preview);
                        }
                    } else {
                        throw new Error('Failed to load content versions');
                    }
                    
                }
                
            } catch (error) {
                console.error('Session restore error:', error);
                
                await HttpService.post('/api/clear-session.php', {});
                $('.step').removeClass('active');
                $('.step-introduction').addClass('active');
            }
        });

        $('#start-new-session').on('click', () => {
            this.startNewSession();
        });
    }

    static async loadAllVersions(currentVersion) {
        try {
            const promises = [];
            // Load all versions up to current version
            for (let i = 1; i <= currentVersion; i++) {
                promises.push(HttpService.get(`/api/get-full-content.php?version=${i}`));
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
            const response = await HttpService.get('/api/check-session.php');
            if (response.success && response.data.version) {
                return response.data.version;
            }
            return false;
        } catch (error) {
            console.error('Error checking session version:', error);
            return false;
        }
    }

    static bindGoodbyeActions() {
        $('.step-goodbye .restart-session').on('click', () => {
            this.startNewSession();
        });

        $('.step-goodbye .view-content').on('click', async () => {
            try {
                const response = await HttpService.get('/api/check-session.php');
                
                if (response.success && response.data.version) {
                    const currentVersionNumber = response.data.version;
                    
                    // Just switch to the correct content step
                    $('.step').removeClass('active');
                    $(`.step-content-${currentVersionNumber}`).addClass('active');
                    
                    // Activate the correct tab
                    $(`.step-content-${currentVersionNumber} .tab-button[data-tab="version-${currentVersionNumber}"]`).click();
                } else {
                    throw new Error('No valid session found');
                }
            } catch (error) {
                console.error('Error getting version:', error);
                await HttpService.post('/api/clear-session.php', {});
            }
        });
    }

    static startNewSession() {
        // Hide the register dash message
        $('#session-restore-overlay .restore-message').hide();
        // Show the loading indicator
        $('#session-restore-overlay .restore-loading').show().find('.loading-indicator').addClass('visible');

        HttpService.post('/api/clear-session.php', {})
            .then(() => {
                location.reload();
            })
            .catch(error => {
                console.error('Session clear error:', error);
            });
    }
}
