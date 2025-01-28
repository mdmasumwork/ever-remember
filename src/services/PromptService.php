<?php
class PromptService {

    private function getQuestionsAndAnswers(
        $deadPersonName, 
        $messageType,
        $relationship,
        $details,
        $accomplishments,
        $tone,
        $finalQuestion
    ) {
        // define associative array to store the questions and answers
        $questionsAndAnswers = [
            "deadPersonName" => [
            "question" => "Who is it you want to write a message about today?",
            "answer" => $deadPersonName
            ],
            "messageType" => [
            "question" => "What kind of message would you like me to write?\n" . 
                    "- A message for cards or flowers (condolence-message)\n" .
                    "- A letter for sympathy (sympathy-letter)\n" .
                    "- A eulogy (eulogy)\n" .
                    "- An obituary or something else (obituary)",
            "answer" => match ($messageType) {
                "condolence-message" => "A message for cards or flowers",
                "sympathy-letter" => "A letter for sympathy",
                "eulogy" => "A eulogy",
                "obituary" => "An obituary or something else",
                default => "Unknown message type"
            }
            ],
            "relationship" => [
            "question" => "Please can you tell me a little more about " . $deadPersonName . "? Let's start with how you knew each other.",
            "answer" => $relationship
            ],
            "details" => [
            "question" => "Thank you for sharing that, and I'm truly sorry for your loss. To help me write something that truly captures " . $deadPersonName . ", please could you tell me a bit more about them? You can start with simple details, like:\n" .
                      "- When and where " . $deadPersonName . " was born\n" .
                      "- Their profession or what they did for a living\n" .
                      "- Hobbies and interests that brought them joy\n" .
                      "- Where they grew up and lived\n" .
                      "- Special memories you cherish\n" .
                      "- Anything else you'd like to include\n\n" . 
                      "It’s completely okay if you’re unsure what to say. Feel free to use the white answer boxes to ask for ideas or guidance whenever you need.",
            "answer" => $details
            ],
            "accomplishments" => [
            "question" => "Would you like to share any of " . $deadPersonName . "’s accomplishments? Were there particular achievements at work or in something they were passionate about? What were they especially good at?\n\n" . 
                      "Remember, you don’t need to stress about finding the perfect words—that’s my job. Just type whatever comes to mind, and I’ll take care of the rest.",
            "answer" => $accomplishments
            ],
            "tone" => [
            "question" => "Thank you! To ensure the message truly honors them, could you let me know the tone that feels most appropriate? Here are some options to choose from:\n" .
                      "- Compassionate – Gentle and heartfelt, ideal for creating a comforting and personal message.\n" .
                      "- Formal – Professional and traditional, suitable for a more dignified tribute.\n" .
                      "- Poetic – Elegant and expressive, focusing on deep emotions for an artistic touch.\n" .
                      "- Uplifting – Positive and hopeful, celebrating the joy and achievements of a life well-lived.",
            "answer" => $tone
            ],
            "finalQuestion" => [
            "question" => "Thank you! Before I begin drafting, is there anything else you’d like me to know about " . $deadPersonName . "?",
            "answer" => $finalQuestion
            ]
        ];

        return $questionsAndAnswers;
    }

    private function getStructuredPrompt($data) {
        $questionsAndAnswers = $this->getQuestionsAndAnswers(
            $data['deadPersonName'],
            $data['messageType'],
            $data['relationship'],
            $data['details'],
            $data['accomplishments'],
            $data['tone'],
            $data['finalQuestion']
        );

        $structuredPrompt = '';
        foreach ($questionsAndAnswers as $key => $value) {
            $structuredPrompt .= 'Question: ' . $value['question'] . "\n\n";
            $structuredPrompt .= 'Answer: ' . $value['answer'] . "\n\n";
        }

        return $structuredPrompt;
    }

    private function getBeginningSystemPrompt($deadPerson) {
        return "You are a highly empathetic and professional condolence and sympathy message assistant. Based on the following inputs, create a heartfelt, meaningful, and respectful content that captures the essence of {$deadPerson}'s life and reflects the user's emotions and relationship with them. Follow these guidelines:\n\n";
    }

    private function getEndingSystemPrompt($deadPerson) {
        return "\n\nKey Requirements:\n" .
               "- Begin the message with an empathetic greeting, such as \"Dear family of {$deadPerson}\" or \"To all who loved them,\" depending on the relationship and context provided. If the recipient is unclear, use a general greeting like \"To all who knew and loved {$deadPerson}.\"\n" .
               "- Maintain a tone that aligns with the user’s selection (e.g., compassionate, formal, poetic, or uplifting). For a compassionate tone, use gentle and heartfelt language that is warm and accessible. For a formal tone, maintain professionalism and dignity without being overly rigid or detached. For a poetic tone, keep it elegant and expressive without being overly ornate. For an uplifting tone, focus on celebrating the joy and achievements of the deceased’s life.\n" .
               "- Respect the sensitivity of the topic by avoiding overly casual or informal language.\n" .
               "- Incorporate the details provided by the user (e.g., relationship, accomplishments, special memories) to make the message personal, meaningful, and reflective of {$deadPerson}'s life.\n" .
               "- Identify the gender of the deceased from the conversation (if mentioned) and use the appropriate pronouns and terms (e.g., \"she/her,\" \"he/him,\" or \"they/them\") throughout the message. If the gender is unclear, use neutral pronouns (\"they/them\") to ensure inclusivity.\n" .
               "- If any answers appear mistyped, misplaced, or unclear, interpret them thoughtfully based on the context and other inputs. Use the surrounding questions and answers to infer the user’s intent while ensuring the output remains meaningful and accurate.\n" .
               "- If any details are vague or missing, thoughtfully expand and infer appropriate attributes based on the context to create a complete and respectful message.\n" .
               "- Ensure coherence and flow by interpreting the context from the questions and answers. Address any unclear or mistyped inputs with thoughtful inferences.\n" .
               "- If humor or lighthearted traits are mentioned, incorporate them subtly and respectfully to reflect the deceased’s personality, without detracting from the message's overall tone.\n" .
               "- Keep the message concise and appropriate for the selected message type (e.g., card, sympathy letter, eulogy, or obituary).\n" .
               "- Close the message with a warm and supportive note, offering comfort and hope to the recipient.";
    }

    public function generatePrompt($data) {
        $beginningSystemPrompt = $this->getBeginningSystemPrompt($data['deadPersonName']);
        $structuredPrompt = $this->getStructuredPrompt($data);
        $enddingSystemPrompt = $this->getEndingSystemPrompt($data['deadPersonName']);
        
        $prompt = $beginningSystemPrompt . $structuredPrompt . $enddingSystemPrompt;

        return $prompt;
    }
}