<?php

require_once __DIR__ . '/../services/OpenAIService.php';

class EulogyController {
    private $openAIService;
    
    public function __construct() {
        $this->openAIService = new OpenAIService();
    }
    
    public function generate($data) {
        try {
            // Validate required fields
            if (empty($data['messageType']) || empty($data['deadPersonName'])) {
                throw new Exception('Missing required fields');
            }

            // Build prompt based on form data
            $prompt = $this->buildPrompt($data);
            
            // Generate content using OpenAI
            return $this->openAIService->generateEulogy($prompt);
            
        } catch (Exception $e) {
            return [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }
    }

    private function buildPrompt($data) {
        $prompt = "Write a {$data['messageType']} for {$data['deadPersonName']}";
        
        if (!empty($data['deadPersonRelation'])) {
            $prompt .= ", who was my {$data['deadPersonRelation']}";
        }

        if (!empty($data['deadPersonDetails'])) {
            $prompt .= ". Details about them: {$data['deadPersonDetails']}";
        }

        if (!empty($data['deadPersonAccomplishment'])) {
            $prompt .= ". Their accomplishments include: {$data['deadPersonAccomplishment']}";
        }

        return $prompt;
    }
}