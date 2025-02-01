class Tabs {
    static init() {
        this.bindTabEvents();
        this.bindCopyEvents();
        this.bindDotNavigation();
        this.bindSwipeNavigation();
    }

    static switchToTab(selectedTabClass, direction = 'right', $step) {
        const $tabContainer = $step.find('.content-tabs');
        const $currentPane = $tabContainer.find('.tab-pane.active');
        const $nextPane = $tabContainer.find(`.tab-pane.${selectedTabClass}`);
        
        // Return if next pane doesn't exist
        if (!$nextPane.length) return;
        
        // Add slide out animation
        $currentPane.addClass(direction === 'right' ? 'slide-left-out' : 'slide-right-out');
        
        // Update navigation within this step
        $tabContainer.find('.tab-button, .dot').removeClass('active');
        $tabContainer.find(`.tab-button[data-tab="${selectedTabClass}"]`).addClass('active');
        $tabContainer.find(`.dot[data-tab="${selectedTabClass}"]`).addClass('active');
        
        // Add slide in animation after brief delay
        setTimeout(() => {
            $currentPane.removeClass('active');
            $nextPane.addClass('active');
            $nextPane.addClass(direction === 'right' ? 'slide-left-in' : 'slide-right-in');
            
            // Clean up classes after animation
            setTimeout(() => {
                $currentPane.removeClass('slide-left-out slide-right-out');
                $nextPane.removeClass('slide-left-in slide-right-in');
            }, 300);
        }, 50);
    }

    static bindTabEvents() {
        $('.tab-button').on('click', function() {
            // Return if already active
            if ($(this).hasClass('active')) return;

            const $step = $(this).closest('.step');
            const selectedTabClass = $(this).data('tab');
            const clickedVersion = parseInt(selectedTabClass.match(/version-(\d)/)[1]);
            const currentVersion = parseInt($step.find('.tab-pane.active').attr('class').match(/version-(\d)/)[1]);
            
            // Determine direction based on version numbers
            const direction = clickedVersion > currentVersion ? 'right' : 'left';
            
            Tabs.switchToTab(selectedTabClass, direction, $step);
        });
    }

    static bindDotNavigation() {
        $('.dot').on('click', function() {
            // Return if already active
            if ($(this).hasClass('active')) return;

            const $step = $(this).closest('.step');
            const selectedTabClass = $(this).data('tab');
            const clickedVersion = parseInt(selectedTabClass.match(/version-(\d)/)[1]);
            const currentVersion = parseInt($step.find('.tab-pane.active').attr('class').match(/version-(\d)/)[1]);
            
            // Determine direction based on version numbers
            const direction = clickedVersion > currentVersion ? 'right' : 'left';
            
            Tabs.switchToTab(selectedTabClass, direction, $step);
        });
    }

    static bindSwipeNavigation() {
        let touchStartX = 0;
        let touchStartY = 0;
        
        $('.step .tab-pane').on('touchstart', function(e) {
            touchStartX = e.touches[0].clientX;
            touchStartY = e.touches[0].pageY;
        });

        $('.step .tab-pane').on('touchend', function(e) {
            const $step = $(this).closest('.step');
            const touchEndX = e.changedTouches[0].clientX;
            const touchEndY = e.changedTouches[0].pageY;
            const currentVersion = parseInt($(this).attr('class').match(/version-(\d)/)[1]);
            
            const xDiff = touchStartX - touchEndX;
            const yDiff = Math.abs(touchStartY - touchEndY);
            const swipeThreshold = 50;
            
            if (yDiff < 40 && Math.abs(xDiff) > swipeThreshold && Math.abs(xDiff) > yDiff * 2) {
                // Check if next/previous pane exists before swipe
                if (xDiff > 0 && currentVersion < 3) {
                    const $nextPane = $step.find(`.tab-pane.version-${currentVersion + 1}`);
                    if ($nextPane.length) {
                        Tabs.switchToTab(`version-${currentVersion + 1}`, 'right', $step);
                    }
                } else if (xDiff < 0 && currentVersion > 1) {
                    const $prevPane = $step.find(`.tab-pane.version-${currentVersion - 1}`);
                    if ($prevPane.length) {
                        Tabs.switchToTab(`version-${currentVersion - 1}`, 'left', $step);
                    }
                }
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