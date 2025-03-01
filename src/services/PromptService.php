<?php
class PromptService {

    private function getQuestionsAndAnswers(
        $deceasedPersonName, 
        $messageType,
        $relationship,
        $details,
        $accomplishments,
        $messageTone,
        $finalQuestion,
        $firstPersonName
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
            ],
            "firstPersonName" => [
                "question" => "What is your name?",
                "answer" => $firstPersonName
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
            $data['finalQuestion'],
            $data['firstPersonName']
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

    private function getBeginningSystemPrompt($deceasedPerson, $messageType, $version = 1) {
        $basePrompt = "You are a professional memorial content writer specializing in creating heartfelt and respectful messages. ";
        
        if ($version > 1) {
            return $basePrompt . "This is version {$version} of the content after the user requested changes. Review the previous version(s) and their feedback carefully to maintain continuity while implementing the requested improvements.\n\n";
        }
        
        return $basePrompt . "Create a " . $this->getContentTypeDescription($messageType) . " that honors {$deceasedPerson}.\n\n";
    }

    private function getContentTypeDescription($messageType) {
        return match($messageType) {
            "condolence message" => "brief, heartfelt condolence message suitable for cards or flowers (2-4 sentences, no formal greeting or closing)",
            "sympathy letter" => "personal sympathy letter (3-4 paragraphs with appropriate greeting and closing)",
            "eulogy" => "touching eulogy to be delivered at the memorial service (5-7 minutes when spoken)",
            "obituary" => "formal obituary announcement following standard newspaper format",
            default => "memorial message"
        };
    }

    private function getEndingSystemPrompt($messageType, $tone, $version = 1) {
        $formatGuidelines = $this->getFormatGuidelines($messageType);
        $toneGuidelines = $this->getToneGuidelines($tone);
        
        $baseGuidelines = "
- Never address the deceased person directly
- Never address the user of the application
- Focus on comforting those grieving
- Use appropriate pronouns based on context
- Maintain consistency with previous versions if applicable\n";

        if ($version > 1) {
            $baseGuidelines .= "- Preserve elements the user liked from previous versions\n";
            $baseGuidelines .= "- Focus on implementing the specific changes requested\n";
        }

        return "\n\nFormat Requirements:\n{$formatGuidelines}\n\nTone Guidelines:\n{$toneGuidelines}\n\nGeneral Guidelines:{$baseGuidelines}";
    }

    private function getFormatGuidelines($messageType) {
        return match($messageType) {
            "condolence message" => 
                "- Keep it brief and focused (2-4 sentences)\n" .
                "- No formal greeting or closing\n" .
                "- Express sympathy and offer comfort\n" .
                "- Suitable for cards or flower arrangements",
            
            "sympathy letter" => 
                "- Start with appropriate greeting (e.g., 'Dear [Family Name] Family')\n" .
                "- 3-4 paragraphs structure\n" .
                "- Include personal memories if available\n" .
                "- End with a warm closing and signature line",
            
            "eulogy" => 
                "- Opening that captures attention\n" .
                "- Balance of personal stories and accomplishments\n" .
                "- Clear structure with smooth transitions\n" .
                "- Meaningful conclusion that honors legacy\n" .
                "- Appropriate length for spoken delivery (5-7 minutes)",
            
            "obituary" => 
                "- Standard newspaper format\n" .
                "- Begin with basic biographical information\n" .
                "- Include family members, career, achievements\n" .
                "- End with funeral arrangement details if provided\n" .
                "- Factual yet respectful tone",
            
            default => "- Maintain appropriate length and structure\n- Include relevant details"
        };
    }

    private function getToneGuidelines($tone) {
        return match($tone) {
            "Compassionate" => 
                "- Use warm, gentle language\n" .
                "- Express genuine empathy\n" .
                "- Focus on comfort and support",
            
            "Formal" => 
                "- Maintain professional language\n" .
                "- Use traditional phrasing\n" .
                "- Respect formal conventions",
            
            "Poetic" => 
                "- Include tasteful metaphors\n" .
                "- Use elegant expressions\n" .
                "- Maintain emotional depth",
            
            "Uplifting" => 
                "- Focus on positive memories\n" .
                "- Celebrate life achievements\n" .
                "- Include hopeful messages",
            
            default => "- Maintain appropriate emotional tone"
        };
    }

    public function generatePrompt($data) {
        $beginningSystemPrompt = $this->getBeginningSystemPrompt(
            $data['deceasedPersonName'], 
            $data['messageType'],
            $data['version']
        );
        $structuredPrompt = $this->getStructuredPrompt($data);      
        $endingSystemPrompt = $this->getEndingSystemPrompt(
            $data['messageType'],
            $data['messageTone'], 
            $data['version']
        );        
        return $beginningSystemPrompt . $structuredPrompt . $endingSystemPrompt;
    }

    /**
     * Generate a prompt for extracting name components from a string
     * 
     * @param string $nameString The string containing a person's name
     * @return string The formatted prompt for name extraction
     */
    public function generateNameExtractionPrompt($nameString) {
        $prompt = "You are a name parser. Extract the first name, middle name (if any), and last name (if any) from this input: \"{$nameString}\". " .
                 "Important rules to follow: " .
                 "1. Ignore phrases like 'my name is', 'I am', 'my neighbor', etc. " .
                 "2. If the input has exactly two name parts (e.g., 'John Smith'), treat them as first_name and last_name (not middle_name). " .
                 "3. Only use middle_name when there are 3 or more name components. " .
                 "4. If uncertain, prefer setting last_name rather than middle_name when there are only two name components. " .
                 "Respond with only a simple JSON object with these exact keys: first_name, middle_name, and last_name.";
        
        return $prompt;
    }
}