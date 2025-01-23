(function ($) {
	"use strict";

	$(window).on("load", function () {
		// $("#fullName").focus();
		// $(".intro").addClass("show");
		// $(".chat-anim:first").addClass("active");
		// centerActiveBlock();
	
		let clickCount = 0;
	
		// function toggleButtonState(inputSelector, buttonSelector) {
		// 	$(inputSelector).on("input", function () {
		// 		const isEmpty = $(this).val().trim() === "";
		// 		$(buttonSelector).prop("disabled", isEmpty).toggleClass("disabled", isEmpty);
		// 	});
		// }
		//
		// toggleButtonState("#fullName", ".introBtn");
		// toggleButtonState("#email", ".email_btn");
		// toggleButtonState("#passedName", ".passedBtn");
		// toggleButtonState("#gender", ".genderBtn");
		// toggleButtonState("#dob", ".dobBtn");
		// toggleButtonState("#relation", ".relationBtn");

		let userName = 'Albert';
        let userNickname = 'Albert';
        let contentType = 'Eulogy';
        let passedName = 'Chris';
		let gender = 'Other';
		let pronoun1 = 'They';
		let pronoun2 = 'Their';
		let pronoun3 = 'Them';

        $(".quick_note_btn").on("click", function () {
            userName = $('#fullName').val();
            userNickname = $('#nickName').val();

            $(".username").html(userNickname);
        });

        $(".cart-item").on("click", function () {
            contentType = $('.content-type.active').attr('data-type');
            $(".content-type-text").html(contentType);
        });

		$("#passedName").on("input", function () {
			const insertedName = $(this).val().trim();
			if (insertedName !== "") {
				$(this).prev("label").text("Their full name");
			} else {
				$(this).prev("label").text("Of course...");
			}
		});

        $(".passedBtn").on("click", function () {
            passedName = $('#passedName').val();
            $(".dead-person").html(passedName);
			$("#relation").attr("placeholder", passedName + " was my...");
			$("#confirmation").attr("placeholder", "Some facts about " + passedName + "...");
			$("#deep-thought").attr("placeholder", "More details about " + passedName);
        });
		

		$("#relation").on("input", function () {
			const relationValue = $(this).val().trim();
			if (relationValue !== "") {
				$(this).prev("label").text(passedName + " was my");
			} else {
				$(this).prev("label").text("Srue thing");
			}
		});
	
		// function handleSectionTransition(buttonSelector, nextSection) {
		// 	$(buttonSelector).on("click", function () {
		// 		$(this).hide();
		// 		$(this).parents(".chat-anim").find(".chat-icon-area").hide();
		// 		$(this).parents(".chat-anim").removeClass("active").css("opacity", 0.4).next().slideDown(300).addClass("active");
		// 		$(this).parents(".chat-anim").find("input, textarea, select").prop("disabled", true);
		// 		if (nextSection) {
		// 			$(this).parents(".chat-anim").next().find(nextSection).show();
		// 		}
		// 		centerActiveBlock();
		// 		$(this).prop("disabled", true);
		// 	});
		// 	$(buttonSelector).parents(".chat-anim").find("input").on("keypress", function (e) {
		// 		if (e.which === 13 && $(this).val().trim() !== "") { // Enter key pressed, field not empty
		// 			$(buttonSelector).click();
		// 			e.preventDefault();
		// 		}
		// 	});
		// }
	
		// $(".introBtn").on("click", function () {
		// 	// $(".test").slideDown();
		// 	$(this).parents(".chat-anim").find(".chat-icon-area").hide();
		// 	$(this).parents(".chat-anim").removeClass("active").css("opacity", 0.4).next().slideDown(300).addClass("active").find(".save-btn").show();
		// 	$(this).parents(".chat-anim").find("input, textarea, select").prop("disabled", true);
		// 	centerActiveBlock();
		// 	$(this).prop("disabled", true);
		// 	$("#email").focus();
		// 	$(this).prop("disabled", true);
		// 	$(".username").html($("#fullName").val());
		// });
		
		// $("#fullName").on("keypress", function (e) {
		// 	if (e.which === 13 && $(this).val().trim() !== "") { // Enter key pressed, field not empty
		// 		e.preventDefault();
		// 		$(".introBtn").click();
		// 	}
		// });
		
		// $(".email_btn").on("click", function () {
		// 	// $(this).hide();
		// 	$(this).parents(".chat-anim").find(".chat-icon-area").hide();
		// 	$(this).parents(".chat-anim").removeClass("active").animate({ opacity: 0.4 }, 800).next().slideDown(300).addClass("active");
		// 	$(this).parents(".chat-anim").prev().animate({ opacity: 0 }, 500);
		// 	$(this).parents(".chat-anim").find("input, textarea, select").prop("disabled", true);
		// 	centerActiveBlock();
		// 	$(this).prop("disabled", true);
		// });
	
		// $("#email").on("keypress", function (e) {
		// 	if (e.which === 13 && $(this).val().trim() !== "") { // Enter key pressed, field not empty
		// 		$(".email_btn").click();
		// 		e.preventDefault();
		// 	}
		// });
		
		// $(".email-skip").on("click", function (e) {
		// 	$(".email_btn").click();
		// 	e.preventDefault();
		// });
	
		// $(".quick_note_btn").on("click", function () {
		// 	// $(this).hide();
		// 	$(this).parents(".chat-anim").find(".chat-icon-area").hide();
		// 	$(this).parents(".chat-anim").removeClass("active").animate({ opacity: 0.4 }, 800).next().slideDown(300).addClass("active");
		// 	$(this).parents(".chat-anim").prev().animate({ opacity: 0 }, 500);
		// 	$(this).parents(".chat-anim").find("input, textarea, select").prop("disabled", true);
		// 	centerActiveBlock();
		// 	$(this).prop("disabled", true);
		// 	$("#passedName").focus();
		// });

		// $(".cart-item").on("click", function () {
		// 	$(this).parents(".chat-anim").find(".chat-icon-area").hide();
		// 	$(this).addClass("active").parents(".chat-anim").removeClass("active").animate({ opacity: 0.4 }, 800).next().slideDown(300).addClass("active");
		// 	$(this).parents(".chat-anim").find("input, textarea, select").prop("disabled", true);
		// 	centerActiveBlock();
		// 	$(this).prop("disabled", true);
		// 	$(this).parents(".chat-anim").next().find("input, textarea, select").first().focus();
		// });
	
		// $(".passedBtn, .genderBtn, .dobBtn, .relationBtn, .ideasBtn, .confirmationBtn, .deepThoughtBtn, .second_email_btn, .feedback_btn").on("click", function () {
		// 	$(this).prop("disabled", true);
		// 	$(this).parents(".chat-anim").find(".chat-icon-area").hide();
		// 	$(this).parents(".chat-anim").removeClass("active").animate({ opacity: 0.4 }, 800);
		// 	$(this).parents(".chat-anim").next().slideDown(500).addClass("active");
		// 	$(this).parents(".chat-anim").find("input, textarea, select").prop("disabled", true);
		// 	centerActiveBlock();
		// 	$(this).parents(".chat-anim").next().promise().done(function() {
		// 		$(this).find("input, textarea, select").first().focus();
		// 	});
		// });
		
		// $(".dob-skip-button").on("click", function (e) {
		// 	$(".dobBtn").click();
		// 	e.preventDefault();
		// });
		
		// $(".ideas-skip-button").on("click", function (e) {
		// 	$(".ideasBtn").click();
		// 	e.preventDefault();
		// });
		
		// $(".second-email-skip").on("click", function (e) {
		// 	$(".second_email_btn").click();
		// 	e.preventDefault();
		// });
		
		// $(".feedback-skip").on("click", function (e) {
		// 	$(".feedback_btn").click();
		// 	e.preventDefault();
		// });

		// $("#passedName, #gender, #dob, #relation").on("keypress", function (e) {
		// 	if (e.which === 13 && $(this).val().trim() !== "") { // Enter key pressed, field not empty
		// 		$(this).parents(".chat-anim").find(".anim-btn").click();
		// 		e.preventDefault();
		// 	}
		// });
		
		// $(".relationBtn").on("click", function () {
		// 	let relation = $("#relation").val();
		// 	$(".relation-in-text").html(relation);
		// });
		
		// $(".response-to-the-content-area .wide-button-area button").on("click", function () {
		// 	// Add 'selected' class to the clicked button
		// 	$(this).addClass('selected');
		//
		// 	// Fade out all other buttons except the clicked one
		// 	$('.response-to-the-content-area .wide-button-area button').not(this).fadeTo(300, 0.5);
		//
		// 	// Optionally, ensure only the clicked button has the 'selected' class
		// 	$('.response-to-the-content-area .wide-button-area button').not(this).hide();
		//
		// 	$(this).prop("disabled", true);
		// 	$(this).parents(".chat-anim").find(".chat-icon-area").hide();
		// 	$(this).parents(".chat-anim").removeClass("active").animate({ opacity: 0.4 }, 800);
		// 	$(this).parents(".chat-anim").next().slideDown(500).addClass("active");
		// 	$(this).parents(".chat-anim").find("input, textarea, select").prop("disabled", true);
		// 	centerActiveBlock();
		// 	$(this).parents(".chat-anim").next().promise().done(function() {
		// 		$(this).find("input, textarea, select").first().focus();
		// 	});
		// });
	
		// $(".deepThoughtBtn").on("click", function () {
		// 	$(this).addClass("edit").html('<img src="assets/images/icons/edit.svg">');
		// 	$(this).hide();
		// 	$(this).parents(".chat-anim").find(".chat-icon-area").hide();
		// 	$(this).parents(".chat-anim").removeClass("active").css("opacity", 0.4).next().slideDown(300).addClass("active").find(".save-btn").show();
		// 	$(this).parents(".chat-anim").find("input, textarea, select").prop("disabled", true);
		// 	centerActiveBlock();
		// 	$(this).prop("disabled", true);
		// });
	
		// $(".finalBtn").on("click", function () {
		// 	$(this).parents(".chat-wrapper").addClass("finalDocument").find(".document-wrap").addClass("show");
		// });
	
		// $(".close_document").on("click", function () {
		// 	$(this).parent().removeClass("show");
		// 	$(this).parents(".chat-wrapper").removeClass("finalDocument");
		// });
	});
})(jQuery);