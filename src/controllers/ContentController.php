<?php

require_once __DIR__ . '/../services/OpenAIService.php';
require_once __DIR__ . '/../services/PromptService.php';
require_once __DIR__ . '/../services/ValidationService.php';
require_once __DIR__ . '/../includes/functions.php';

class ContentController {
    private $openAIService;
    private $promptService;
    private $validationService;

    public function __construct() {
        $this->openAIService = new OpenAIService();
        $this->promptService = new PromptService();
        $this->validationService = new ValidationService();
    }

    public function handleRequest() {

        //add a sleep for 2 seconds.
        // sleep(1);

        session_start();
        
        // Add content-type validation
        if (!empty($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') === false) {
            return sendResponse(false, ['error' => 'Invalid content type']);
        }

        $jsonData = file_get_contents('php://input');
        
        // Validate JSON structure
        if (!$this->isValidJson($jsonData)) {
            return sendResponse(false, ['error' => 'Invalid JSON format']);
        }
        
        $data = json_decode($jsonData, true);

        try {
            // Single validation call that handles all checks
            $this->validationService->validateRequest($data, $_SESSION);

            // First time request
            if (!isset($_SESSION['version'])) {
                session_regenerate_id(true);
                $_SESSION = array();
                $_SESSION['version'] = 1;
                $_SESSION['form_data'] = $data;
                $_SESSION['contents'] = array();
            } else {
                $_SESSION['version']++;
                $_SESSION['form_data']['additionalInstructions'][] = $data['additionalInstruction'] ?? '';
                $data = $_SESSION['form_data'];
                
                if (isset($_SESSION['contents'])) {
                    $data['contents'] = $_SESSION['contents'];
                }
            }

            $data['version'] = $_SESSION['version'] ?? 1;
            $prompt = $this->promptService->generatePrompt($data);
            $generatedContent = $this->openAIService->generateContent($prompt);                

            // Store content by version
            $_SESSION['contents'][$data['version']] = $generatedContent['content'];

            return sendResponse(true, [
                'preview' => substr($generatedContent['content'], 0, 100) . '...',
                'fullContent' => $generatedContent['content'],
                'prompt' => $prompt,
                'version' => $data['version'],
                'payment_verified' => $_SESSION['payment_verified'] ?? false
            ]);

        } catch (ValidationException $e) {
            return sendResponse(false, ['error' => $e->getMessage()]);
        } catch (Exception $e) {
            return sendResponse(false, ['error' => 'An unexpected error occurred']);
        }
    }

    private function isValidJson($string) {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}