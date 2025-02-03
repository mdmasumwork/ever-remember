<?php
class PromptService {

    private function getQuestionsAndAnswers(
        $deceasedPersonName, 
        $messageType,
        $relationship,
        $details,
        $accomplishments,
        $messageTone,
        $finalQuestion
    ) {
        // define associative array to store the questions and answers
        $questionsAndAnswers = [
            "deceasedPersonName" => [
            "question" => "Who is it you want to write a message about today?",
            "answer" => $deceasedPersonName
            ],
            "messageType" => [
            "question" => "What kind of message would you like me to write?\n" . 
                    "- A message for cards or flowers (condolence-message)\n" .
                    "- A letter for sympathy (sympathy-letter)\n" .
                    "- A eulogy (eulogy)\n" .
                    "- An obituary or something else (obituary)",
            "answer" => match ($messageType) {
                "condolence message" => "A message for cards or flowers",
                "sympathy letter" => "A letter for sympathy",
                "eulogy" => "A eulogy",
                "obituary" => "An obituary or something else",
                default => "Unknown message type"
            }
            ],
            "relationship" => [
            "question" => "Please can you tell me a little more about " . $deceasedPersonName . "? Let's start with how you knew each other.",
            "answer" => $relationship
            ],
            "details" => [
            "question" => "Thank you for sharing that, and I'm truly sorry for your loss. To help me write something that truly captures " . $deceasedPersonName . ", please could you tell me a bit more about them? You can start with simple details, like:\n" .
                      "- When and where " . $deceasedPersonName . " was born\n" .
                      "- Their profession or what they did for a living\n" .
                      "- Hobbies and interests that brought them joy\n" .
                      "- Where they grew up and lived\n" .
                      "- Special memories you cherish\n" .
                      "- Anything else you'd like to include\n\n" . 
                      "It’s completely okay if you’re unsure what to say. Feel free to use the white answer boxes to ask for ideas or guidance whenever you need.",
            "answer" => $details
            ],
            "accomplishments" => [
            "question" => "Would you like to share any of " . $deceasedPersonName . "’s accomplishments? Were there particular achievements at work or in something they were passionate about? What were they especially good at?\n\n" . 
                      "Remember, you don’t need to stress about finding the perfect words—that’s my job. Just type whatever comes to mind, and I’ll take care of the rest.",
            "answer" => $accomplishments
            ],
            "messageTone" => [
            "question" => "Thank you! To ensure the message truly honors them, could you let me know the tone that feels most appropriate? Here are some options to choose from:\n" .
                      "- Compassionate – Gentle and heartfelt, ideal for creating a comforting and personal message.\n" .
                      "- Formal – Professional and traditional, suitable for a more dignified tribute.\n" .
                      "- Poetic – Elegant and expressive, focusing on deep emotions for an artistic touch.\n" .
                      "- Uplifting – Positive and hopeful, celebrating the joy and achievements of a life well-lived.",
            "answer" => $messageTone
            ],
            "finalQuestion" => [
            "question" => "Thank you! Before I begin drafting, is there anything else you’d like me to know about " . $deceasedPersonName . "?",
            "answer" => $finalQuestion
            ]
        ];

        return $questionsAndAnswers;
    }

    private function getStructuredPrompt($data) {
        $questionsAndAnswers = $this->getQuestionsAndAnswers(
            $data['deceasedPersonName'],
            $data['messageType'],
            $data['relationship'],
            $data['details'],
            $data['accomplishments'],
            $data['messageTone'],
            $data['finalQuestion']
        );

        $structuredPrompt = '';
        foreach ($questionsAndAnswers as $key => $value) {
            $structuredPrompt .= 'Question: ' . $value['question'] . "\n\n";
            $structuredPrompt .= 'Answer: ' . $value['answer'] . "\n\n";
        }
        
        // Add the restored content from the session
        if ((isset($data['contents'][1]) && $data['version'] === 2) || (isset($data['contents'][2]) && $data['version'] === 3)) {
            $structuredPrompt .= "First version:\n" . $data['contents'][1] . "\n\n";

            $structuredPrompt .= "Question: Did you like it?\n\n";
            $structuredPrompt .= "Answer: No, I want changes.\n\n";

            $structuredPrompt .= "Question: Ok, no problem. Would you like to share anything more about {$data['deceasedPersonName']} or provide any specific instructions to help improve the content? But no worries! if you don't have additional details or instructions, I'll generate another version of the content.\n\n"; 
            $structuredPrompt .= "Answer: " . $data['additionalInstructions'][0] . "\n\n";
        }


        if (isset($data['contents'][2]) && $data['version'] === 3) {
            $structuredPrompt .= "Second version:\n" . $data['contents'][2] . "\n\n";

            $structuredPrompt .= "Question: Does this revised content meet your expectations?\n\n";
            $structuredPrompt .= "Answer: No, I still need it to be improved.\n\n";

            $structuredPrompt .= "Question: OK, could you please provide any additional instructions or specific details to help us craft a version that truly meets your expectations. But it's totally fine if you don’t have additional details or instructions, I’ll still generate another version of the content.\n\n";
            $structuredPrompt .= "Answer: " . $data['additionalInstructions'][1] . "\n\n";
        }

        return $structuredPrompt;
    }

    private function getBeginningSystemPrompt($deceasedPerson, $version = 1) {
        if ($version === 2) {
            return "You are a highly empathetic and professional condolence and sympathy message assistant.The user has already received a first version of the message and now wants to refine or modify it based on additional instructions. Your task is to follow the user's instructions carefully while also improving the message based on the details provided in the conversation.\n\n";
        } elseif ($version === 3) {
            return "You are a highly empathetic and professional condolence and sympathy message assistant. The user has already received a refined version of the message but wants to make further modifications based on additional instructions. Your task is to follow the user's instructions carefully while also improving the message based on the details provided in the conversation.\n\n";
        } else {
            return "You are a highly empathetic and professional condolence and sympathy message assistant. Based on the following inputs, create a heartfelt, meaningful, and respectful content that captures the essence of {$deceasedPerson}'s life and reflects the user's emotions and relationship with them. Follow these guidelines:\n\n";
        }
    }

    private function getEndingSystemPrompt($deceasedPerson, $version = 1) {
        if ($version === 2) {
            return "\n\nKey Requirements for Refinement:\n" .
                   "- Follow the user's instructions exactly as much as possible.\n" .
                   "- Make necessary refinements based on the conversation (questions, answers, and first version).\n" .
                   "- Ensure coherence, clarity, and meaningful expression while improving readability.\n" .
                   "- Maintain warmth, empathy, and an appropriate tone based on the user's selected style.\n" .
                   "- Keep the response respectful, heartfelt, and fitting for the intended purpose.";
        } elseif ($version === 3) {
            return "\n\nKey Requirements for Further Refinement:\n" .
                    "- Follow the user's instructions exactly as much as possible.\n" .
                    "- Make necessary refinements based on the conversation (questions, answers, and previous versions).\n" .
                    "- Ensure coherence, clarity, and meaningful expression while improving readability.\n" .
                    "- Maintain warmth, empathy, and an appropriate tone based on the user's selected style.\n" .
                    "- Keep the response respectful, heartfelt, and fitting for the intended purpose.";
        } else {
            return "\n\nKey Requirements:\n" .
                   "- Begin the message with an empathetic greeting, such as \"Dear family of {$deceasedPerson}\" or \"To all who loved them,\" depending on the relationship and context provided. If the recipient is unclear, use a general greeting like \"To all who knew and loved {$deceasedPerson}.\"\n" .
                   "- Maintain a tone that aligns with the user’s selection (e.g., compassionate, formal, poetic, or uplifting). For a compassionate tone, use gentle and heartfelt language that is warm and accessible. For a formal tone, maintain professionalism and dignity without being overly rigid or detached. For a poetic tone, keep it elegant and expressive without being overly ornate. For an uplifting tone, focus on celebrating the joy and achievements of the deceased’s life.\n" .
                   "- Respect the sensitivity of the topic by avoiding overly casual or informal language.\n" .
                   "- Incorporate the details provided by the user (e.g., relationship, accomplishments, special memories) to make the message personal, meaningful, and reflective of {$deceasedPerson}'s life.\n" .
                   "- Identify the gender of the deceased from the conversation (if mentioned) and use the appropriate pronouns and terms (e.g., \"she/her,\" \"he/him,\" or \"they/them\") throughout the message. If the gender is unclear, use neutral pronouns (\"they/them\") to ensure inclusivity.\n" .
                   "- If any answers appear mistyped, misplaced, or unclear, interpret them thoughtfully based on the context and other inputs. Use the surrounding questions and answers to infer the user’s intent while ensuring the output remains meaningful and accurate.\n" .
                   "- If any details are vague or missing, thoughtfully expand and infer appropriate attributes based on the context to create a complete and respectful message.\n" .
                   "- Ensure coherence and flow by interpreting the context from the questions and answers. Address any unclear or mistyped inputs with thoughtful inferences.\n" .
                   "- If humor or lighthearted traits are mentioned, incorporate them subtly and respectfully to reflect the deceased’s personality, without detracting from the message's overall tone.\n" .
                   "- Keep the message concise and appropriate for the selected message type (e.g., card, sympathy letter, eulogy, or obituary).\n" .
                   "- Close the message with a warm and supportive note, offering comfort and hope to the recipient.";
        }
    }

    public function generatePrompt($data) {
        $beginningSystemPrompt = $this->getBeginningSystemPrompt($data['deceasedPersonName'], $data['version']);
        $structuredPrompt = $this->getStructuredPrompt($data);      
        $enddingSystemPrompt = $this->getEndingSystemPrompt($data['deceasedPersonName'], $data['version']);        
        return $beginningSystemPrompt . $structuredPrompt . $enddingSystemPrompt;
    }
}