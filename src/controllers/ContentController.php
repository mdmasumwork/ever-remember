<?php

require_once __DIR__ . '/../services/OpenAIService.php';
require_once __DIR__ . '/../services/PromptService.php';
require_once __DIR__ . '/../services/ValidationService.php';
require_once __DIR__ . '/../services/SessionService.php';
require_once __DIR__ . '/../includes/functions.php';

class ContentController {
    private $openAIService;
    private $promptService;
    private $validationService;
    private $sessionService;

    public function __construct() {
        $this->openAIService = new OpenAIService();
        $this->promptService = new PromptService();
        $this->validationService = new ValidationService();
        $this->sessionService = new SessionService();
    }

    public function handleRequest() {
        try {
            
            // Validate JSON and content type
            if (!empty($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') === false) {
                return sendResponse(false, ['error' => 'Invalid content type']);
            }

            $data = $_SESSION['form_data'];

            // Check if user has agreed to terms (for first version)
            if (!isset($_SESSION['version']) && (!isset($data['termsAgreed']) || $data['termsAgreed'] !== true)) {
                return sendResponse(false, ['error' => 'You must agree to the Terms of Service and Privacy Policy']);
            }

            // Explicitly specify content type validation
            $sanitizedData = $this->validationService->validateAndSanitize(
                $data, 
                $_SESSION, 
                ValidationService::TYPE_CONTENT
            );
                  
            
            if (!isset($_SESSION['version'])) {
                $this->sessionService->createSession();
                $_SESSION['version'] = 1;
                $_SESSION['form_data'] = $sanitizedData;
                $_SESSION['contents'] = array();
            } else {
                $_SESSION['version']++;
                $sanitizedData = $_SESSION['form_data'];
                
                if (isset($_SESSION['contents'])) {
                    $sanitizedData['contents'] = $_SESSION['contents'];
                }
            }

            $sanitizedData['version'] = $_SESSION['version'] ?? 1;

            // Process and generate content
            $prompt = $this->promptService->generatePrompt();
            $generatedContent = $this->openAIService->generateContent($prompt);

            $_SESSION['contents'][$sanitizedData['version']] = $generatedContent['content'];

            LogUtil::log('content', '[SessionID: ' . session_id() . '][Content]: Version ' . $sanitizedData['version'] . ' of the content has been generated');

            return sendResponse(true, [
                'content' => $generatedContent['content'],
                'prompt' => $prompt,
                'version' => $sanitizedData['version'],
                'payment_verified' => $_SESSION['payment_verified'] ?? false
            ]);

        } catch (ValidationException $e) {
            LogUtil::log('error', '[Exception]: ContentContorller:handleRequest(): ' . $e->getMessage());
            return sendResponse(false, ['error' => $e->getMessage()]);
        } catch (Exception $e) {
            LogUtil::log('error', '[Exception]: ContentContorller:handleRequest(): ' . $e->getMessage());
            return sendResponse(false, ['error' => 'An unexpected error occurred']);
        }
    }

    private function isValidJson($string) {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}