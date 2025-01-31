<?php

require_once __DIR__ . '/../services/OpenAIService.php';
require_once __DIR__ . '/../services/PromptService.php';
require_once __DIR__ . '/../includes/functions.php';

class ContentController {
    private $openAIService;
    private $promptService;

    public function __construct() {
        $this->openAIService = new OpenAIService();
        $this->promptService = new PromptService();
    }

    public function handleRequest() {
        session_start();

        $jsonData = file_get_contents('php://input');
        $data = json_decode($jsonData, true);

        if (!$data) {
            return sendResponse(false, ['error' => 'Invalid input data']);
        }

        // Reset session for first request
        if (isset($data['isFirstRequest']) && $data['isFirstRequest'] === true) {
            session_regenerate_id(true);
            $_SESSION = array();
        }

        // Code to delay 3 seconds
        sleep(3);
        
        // Get POST data
        $jsonData = file_get_contents('php://input');
        $data = json_decode($jsonData, true);

        if (!$data) {
            return sendResponse(false, ['error' => 'Invalid input data']);
        }
        

        try {

            // First time request
            if (!isset($_SESSION['version'])) {
                session_regenerate_id(true);
                $_SESSION = array();
                $_SESSION['version'] = 1;
                $_SESSION['form_data'] = $data;
                $_SESSION['contents'] = array(); // Initialize contents array
            } else {
                $_SESSION['version']++;
                $_SESSION['form_data']['additionalInstructions'][] = $data['additionalInstruction'];
                $data = $_SESSION['form_data'];
                
                if (isset($_SESSION['contents'])) {
                    $data['contents'] = $_SESSION['contents'];
                }
            }

            // Additional versions (2nd or 3rd)
            if ($_SESSION['version'] > 3) {
                return sendResponse(false, ['error' => 'You have reached the maximum number of versions allowed']);
            }

            // if the payment is not verified after the first verstion
            if ($_SESSION['version'] > 1 && (!isset($_SESSION['payment_verified']) || !$_SESSION['payment_verified'])) {
                return sendResponse(false, ['error' => 'Payment required']);
            }

            $data['version'] = $_SESSION['version'] ?? 1;
            $prompt = $this->promptService->generatePrompt($data);
            $generatedContent = $this->openAIService->generateContent($prompt);                

            // Store content by version
            $_SESSION['contents'][$data['version']] = $generatedContent['content'];
            
            // Return preview for first version, full content for paid versions
            if ($data['version'] === 1) {
                return sendResponse(true, [
                    'preview' => substr($generatedContent['content'], 0, 100) . '...',
                    'version' => $data['version'],
                    'payment_verified' => $_SESSION['payment_verified'] ?? false
                ]);
            } else {
                return sendResponse(true, [
                    'fullContent' => $generatedContent['content'],
                    'version' => $data['version'],
                    'payment_verified' => $_SESSION['payment_verified']
                ]);
            }

        } catch (Exception $e) {
            return sendResponse(false, ['error' => $e->getMessage()]);
        }
    }
}