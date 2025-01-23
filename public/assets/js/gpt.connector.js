(function ($) {
    "use strict";

    $(document).ready(function() {

        let userName = 'Albert';
        let contentType = 'Eulogy';
        let passedName = 'Chris';

        $('#generate-content-button').click(function() {
            userName = $('#fullName').val();
            // userNickname = $('#nickName').val();
            contentType = $('.content-type.active').attr('data-type');
            passedName = $('#passedName').val();
            const relation = $('#relation').val();
            const contentTone = $('.content-tone.active').attr('data-tone');
            const confirmation = $('#confirmation').val();
            const deepThought = $('#deep-thought').val();
            // const gender = $('#gender').val();
            // const dob = $('#dob').val();

            let pronoun1 = 'They';
            let pronoun2 = 'Their';
            let pronoun3 = 'Them';
            // if (gender == "male") {
			// 	pronoun1 = 'he';
			// 	pronoun2 = 'his';
			// 	pronoun3 = 'him';
			// } else if (gender == 'female') {
			// 	pronoun1 = 'she';
			// 	pronoun2 = 'her';
			// 	pronoun3 = 'her';
			// }
			$(".pronoun1").html(pronoun1);
			$(".pronoun2").html(pronoun2);
			$(".pronoun3").html(pronoun3);

            const prompt = `
                Hello, I need your help writing a ${contentType}. 

                My name is ${userName}. 
                The content is for ${passedName}, who was my ${relation}.

                I'd like the tone to be ${contentTone}. 
                Here are some facts about ${passedName} to help personalize the content:

                ${confirmation}

                Here are some more details about ${passedName} to help further personalize the content:

                ${deepThought}

                Please craft something thoughtful and meaningful using this information. There's no need to address me or our conversation, just provide the requested content. Thank you.
            `;



            console.log(prompt);

            const apiKey = "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";
            
            $.ajax({
                url: 'https://api.openai.com/v1/chat/completions',
                type: 'POST',
                contentType: 'application/json',
                headers: {
                    'Authorization': 'Bearer ' + apiKey
                },
                data: JSON.stringify({
                    model: "gpt-3.5-turbo",
                    messages: [
                        { role: "system", content: "You are an AI that helps users write personalized content." },
                        { role: "user", content: prompt }
                    ],
                    max_tokens: 500
                }),
                success: function(response) {
                    const generatedText = response.choices[0].message.content.trim();
                    console.log(generatedText);
                    $('#generated-content-body').html('<p>' + generatedText.replace(/\n/g, '</p><p>') + '</p>');
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        });
    });

})(jQuery);
