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
        
        // Get POST data
        $jsonData = file_get_contents('php://input');
        $data = json_decode($jsonData, true);

        if (!$data) {
            return sendResponse(false, ['error' => 'Invalid input data']);
        }

        // Generate prompt
        $prompt = $this->promptService->generatePrompt($data);

        try {
            // Get content from OpenAI
            $generatedContent = $this->openAIService->generateContent($prompt);

            // Store in session
            $_SESSION['full_content'] = $generatedContent['content'];
            $_SESSION['version'] = ($_SESSION['version'] ?? 0) + 1;

            // Return both preview and full content
            return sendResponse(true, [
                'preview' => substr($generatedContent['content'], 0, 100) . '...',
                'fullContent' => $generatedContent['content'], // Add full content
                'version' => $_SESSION['version'],
                'remainingVersions' => 3 - $_SESSION['version'],
                'isPaid' => isset($_SESSION['payment_verified']) && $_SESSION['payment_verified'] === true
            ]);

        } catch (Exception $e) {
            return sendResponse(false, ['error' => $e->getMessage()]);
        }
    }
}