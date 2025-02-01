class CardComponent {
    static init() {
        this.bindCardInteractions();
    }

    static bindCardInteractions() {
        $('.card').on('click', function() {
            const $step = $(this).closest('step');
            $step.find('.card').removeClass('selected');
            $(this).addClass('selected');
        });

        $('.card').on('keydown', function(e) {
            if (e.keyCode === 13 || e.keyCode === 32) { // Enter or Space
                e.preventDefault();
                const $step = $(this).closest('step');
                $step.find('.card').removeClass('selected');
                $(this).addClass('selected');
            }
        });
    }
}