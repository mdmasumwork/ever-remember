class UIManager {
    static updateContentStep(content, version, isPaid = false) {

        $('.loading-indicator').hide();
        
        if (version === 1 && !isPaid) {
            // First version, not paid
            $(`.tab-pane.version-${version} .generated-content`)
                .html(content)
                .addClass('visible');
        } else {
            // Paid versions or after payment
            $(`.tab-pane.version-${version} .generated-content`)
                .html(content)
                .addClass('visible');
            
            // Show copy actions for this version
            $(`.step-content-${version} .tab-pane.version-${version} .content-copy-actions`).addClass('visible');
            
            // Hide payment overlay if showing
            $('.payment-overlay').hide();
            $('.payment-success-overlay').removeClass('visible');
            $('.content-box').removeClass('masked');
            $('.content-actions .content-action').addClass('visible');
        }
        
        $(`.step-content-${version} .content-actions`).addClass('visible');

    }
}