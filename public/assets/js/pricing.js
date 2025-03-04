$(document).ready(function() {
    // Handle click events for pricing cards
    $('.pricing-card').on('click', function() {
        // Get the package type from data attribute
        const packageType = $(this).data('package');
        
        // Redirect to home page
        window.location.href = '/';
    });
    
    // Handle click events specifically for the buttons
    $('.select-plan-btn').on('click', function(e) {
        // Prevent the event from bubbling up to the card
        e.stopPropagation();
        
        // Get the package type from the parent card
        const packageType = $(this).closest('.pricing-card').data('package');
        
        // Redirect to home page
        window.location.href = '/';
    });
    
    SlideMenu.init();
});
