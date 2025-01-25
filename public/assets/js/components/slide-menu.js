class SlideMenu {
    static init() {
        this.bindEvents();
    }

    static bindEvents() {
        $('.burger-menu').on('click', function() {
            $('#slide-menu').addClass('open');
        });

        $('.close-icon').on('click', function() {
            $('#slide-menu').removeClass('open');
        });

        // Close menu when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#slide-menu, .burger-menu').length) {
                $('#slide-menu').removeClass('open');
            }
        });
    }
}