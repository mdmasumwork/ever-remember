<?php

require_once __DIR__ . '/../config/openai.php';

class OpenAIService {
    private $apiKey;
    private $model;
    private $temperature;
    
    public function __construct() {
        $this->apiKey = OpenAIConfig::get('api_key');
        $this->model = OpenAIConfig::get('model');
        $this->temperature = (float) OpenAIConfig::get('temperature');
    }
    
    public function generateContent($prompt) {
        
        $version = isset($_SESSION['version']) ? $_SESSION['version'] : 'unknown';
        return [
            'content' => 'version ' . $version . ' Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus ut metus felis. Nulla lobortis velit elit, et interdum nunc euismod vel. Fusce laoreet a est in posuere. Sed egestas ut nunc eu efficitur. Donec est ex, auctor non pharetra viverra, scelerisque nec mauris. Nam metus'
        ];
    }
    
    public function generateContent2($prompt) {
        $url = 'https://api.openai.com/v1/chat/completions';
        
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey
        ];
        
        $data = [
            'model' => $this->model,
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => $this->temperature
        ];
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            // Log detailed error information
            $errorMessage = 'OpenAI API request failed with status ' . $httpCode . ' Response: ' . $response;
            throw new Exception($errorMessage);
        }
        
        $responseData = json_decode($response, true);
        
        // Format response to match ContentController expectations
        return [
            'content' => $responseData['choices'][0]['message']['content']
        ];
    }
}