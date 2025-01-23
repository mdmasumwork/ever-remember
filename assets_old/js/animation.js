(function ($) {
    "use strict";
    
    $(document).ready(function () {
        
        $(".chat-anim:first").addClass("active show").find("input, textarea").focus();
        centerActiveBlock();
        
        // On page load, disable all buttons inside .required sections
        $(".chat-anim.required .anim-btn").prop("disabled", true);
        
        // Add event listener to input and textarea fields within .chat-anim
        $(".chat-anim").on("input", "input, textarea", function () {
            const $button = $(this).closest(".chat-anim").find(".anim-btn");
            const inputValue = $(this).val().trim();

            if (inputValue) {
                $button.prop("disabled", false);
            } else {
                $button.prop("disabled", true);
            }
        });
        
        // Trigger button click when the user presses Enter in an input or textarea
        $(".chat-anim").on("keypress", "input, textarea", function (e) {
            if (e.which === 13) {
                e.preventDefault();
                const $this = $(this);
                const $button = $this.parents(".chat-anim").find(".anim-btn");
                
                if (!$button.prop("disabled")) {
                    $button.trigger("click");
                }
            }
        });
        
        // Make the next section active and call for the animation.
        $(".chat-anim .anim-btn").on("click", function () {
            animation($(this).parents(".chat-anim"));
        });
        
        $(".chat-anim .skip-btn").on("click", function () {
            const chat_anim = $(this).parents(".chat-anim");
            chat_anim.find('input, textarea').val("");
            animation(chat_anim);
        });
        
        $(".cart-item").on("click", function () {
            $(this).addClass('active');
            animation($(this).parents(".chat-anim"))
        });
        
        $(".response-to-the-content-area .wide-button-area button").on("click", function () {
            const all_response_wide_buttons = $('.response-to-the-content-area .wide-button-area button');
            
            $(this).addClass('selected');
            all_response_wide_buttons.not(this).fadeTo(300, 0.5);
            all_response_wide_buttons.not(this).hide();
            
            animation($(this).parents(".chat-anim"));
        });
    });
    
    function animation(chat_anim) {
        if (chat_anim.hasClass('last-block')) {
            return;
        }
        chat_anim.find(".chat-icon-area").hide(); // Hiding the flower icon from the bottom
        chat_anim.removeClass("active").css("opacity", 0.4).next().slideDown(300).addClass("active").each(function () {
            const nextChatAnim = $(this);
            
            // Check for input or textarea fields
            const inputField = nextChatAnim.find('input, textarea');
            if (inputField.length > 0) {
                inputField.first().focus(); // Focus on the first input or textarea
            } else {
                const animButton = nextChatAnim.find('.anim-btn');
                if (animButton.length > 0) {
                    animButton.focus(); // Focus on the button if no input or textarea
                }
            }
        });
        chat_anim.find("input, textarea, select, button").prop("disabled", true);
        centerActiveBlock();
    }
    
    
    
    function centerActiveBlock() {
        const $activeBlock = $(".chat-anim.active");
        const containerHeight = $(".chat-inner").height();
        const offset = containerHeight * 0.25;
        let activeBlockTop= $activeBlock.position().top;
        const newTop = offset - activeBlockTop;
        
        // Animate the container to move the active block to the center
        if ($activeBlock.hasClass('first-block')) {
            $(".chat-inner").css("top", 128);
        } else {
            $(".chat-inner").css("top", newTop);
        }
    }
    
})(jQuery);