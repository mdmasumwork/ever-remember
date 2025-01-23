(function ($) {
    "use strict";
    
    // Create a global object to hold variables
    window.chatVariables = {
        full_name: 'Albert',
        email: '',
        dead_person_name: 'Chris',
        content_type: 'Eulogy',
        relation: 'Friend',
        dead_person_dob: '',
        content_tone: '',
        confirmation: '',
        ideas: '',
        deep_thought: '',
        response_to_content: '',
        second_email: '',
        text_feedback: ''
    };
    
    $(document).ready(function() {
        $(".chat-anim .anim-btn").on("click", function () {
            const chat_anim = $(this).closest(".chat-anim");
            const inputField = chat_anim.find("input, textarea").first(); // Get the first input or textarea

            if (inputField.length > 0) {
                const fieldName = inputField.attr("name"); // Get the name attribute
                const fieldValue = inputField.val(); // Get the value of the input

                if (fieldName && chatVariables.hasOwnProperty(fieldName)) {
                    chatVariables[fieldName] = fieldValue;
                }
                
                if (fieldName === 'full_name') {
                    $(".username").html(fieldValue);
                } else if (fieldName === 'dead_person_name') {
                    $(".dead-person").html(fieldValue);
                    $("#relation").attr("placeholder", fieldValue + " was my...");
                    $("#confirmation").attr("placeholder", "Some facts about " + fieldValue + "...");
                    $("#deep-thought").attr("placeholder", "More details about " + fieldValue);
                }
            }
        });
        
        $(".cart-item.content-type").on("click", function () {
            chatVariables['content_type'] = $(this).attr('data-type');
            $(".content-type-text").html(chatVariables['content_type']);
        });
        
        $(".cart-item.content-tone").on("click", function () {
            chatVariables['content_tone'] = $(this).attr('data-tone');
        });
        
        $(".anim-btn.response-to-content-button").on("click", function () {
            chatVariables['response_to_content'] = $(this).attr('data-response');
        });

        $("#generate-content-button").on("click", () => {
            console.log(chatVariables);
            const prompt = `
                Hello, I need your help writing a ${chatVariables['content_type']}.

                Following are the questions we asked to our client regarding their beloved dead person. Our client's name is ${chatVariables['full_name']}.
                
                Question: Full name of the dead person?
                Answer: ${chatVariables['dead_person_name']}
                
                Question: How you knew each other?
                Answer: ${chatVariables['relation']}
                
                Question: Would you also tell me when the person was born or their age?
                Answer: ${chatVariables['dead_person_dob']}
                
                Question: How should be the content tone?
                Answer: ${chatVariables['content_tone']}
                
                Question: To help us write something that truly captures ${chatVariables['dead_person_name']}, please could you tell us a bit more about them?
                Answer: ${chatVariables['confirmation']}
                
                Question: What about ${chatVariables['dead_person_name']}' accomplishments? Was there anything in particular they achieved at work? Or what was they good at? You don't need to worry about finding the right words. Type anything that comes to mine.
                Answer: ${chatVariables["ideas"]}
                
                Question: Ok, is there anything more you'd like to tell me about ${chatVariables['dead_person_name']}, before I draft something?
                Answer: ${chatVariables['deep_thought']}
                
                OK, based on the above conversation you have to write ${chatVariables['content_type']} for ${chatVariables['dead_person_name']} on behalf of ${chatVariables['full_name']}.
                I'd like the tone to be ${chatVariables['content_tone']}.

                Please craft something thoughtful and meaningful using this information. There's no need to address me or our client ${chatVariables['full_name']}, just provide the requested content. Thank you.
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
