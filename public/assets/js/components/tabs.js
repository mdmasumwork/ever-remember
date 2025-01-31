class Tabs {
    static init() {
        this.bindTabEvents();
        this.bindCopyEvents();
        this.bindDotNavigation();
        this.bindSwipeNavigation();
    }

    static switchToTab(selectedTabClass) {
        const $tabContainer = $('.content-tabs');
        $tabContainer.find('.tab-button, .dot').removeClass('active');
        $tabContainer.find('.tab-pane').removeClass('active');
        
        $tabContainer.find(`.tab-button[data-tab="${selectedTabClass}"]`).addClass('active');
        $tabContainer.find(`.dot[data-tab="${selectedTabClass}"]`).addClass('active');
        $tabContainer.find(`.tab-pane.${selectedTabClass}`).addClass('active');
    }

    static bindTabEvents() {
        $('.tab-button').on('click', function() {
            const $tabContainer = $(this).closest('.content-tabs');
            const selectedTabClass = $(this).data('tab');
            
            $tabContainer.find('.tab-button').removeClass('active');
            $tabContainer.find('.tab-pane').removeClass('active');
            
            $(this).addClass('active');
            $tabContainer.find('.tab-pane.' + selectedTabClass).addClass('active');
        });
    }

    static bindDotNavigation() {
        $('.dot').on('click', function() {
            Tabs.switchToTab($(this).data('tab'));
        });
    }

    static bindSwipeNavigation() {
        let touchStartX = 0;
        
        $('.tab-pane').on('touchstart', function(e) {
            touchStartX = e.touches[0].clientX;
        });

        $('.tab-pane').on('touchend', function(e) {
            const touchEndX = e.changedTouches[0].clientX;
            const currentVersion = parseInt($(this).attr('class').match(/version-(\d)/)[1]);
            const swipeThreshold = 50;

            if (touchStartX - touchEndX > swipeThreshold && currentVersion < 3) {
                Tabs.switchToTab(`version-${currentVersion + 1}`);
            } else if (touchEndX - touchStartX > swipeThreshold && currentVersion > 1) {
                Tabs.switchToTab(`version-${currentVersion - 1}`);
            }
        });
    }

    static bindCopyEvents() {
        $('.copy-icon').on('click', function() {
            const $tabPane = $(this).closest('.tab-pane');
            const $contentBox = $tabPane.find('.content-box');
            
            if (!$contentBox.length) return;

            const textToCopy = $contentBox.text().trim();
            const $textarea = $('<textarea>')
                .val(textToCopy)
                .css('position', 'fixed')
                .appendTo('body');
            
            $textarea.select();
            document.execCommand('copy');
            $textarea.remove();
            
            $(this).addClass('copied');
            Toast.show();
            
            setTimeout(() => {
                $(this).removeClass('copied');
            }, 2000);
        });
    }
}