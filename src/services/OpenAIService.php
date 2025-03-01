<?php

require_once __DIR__ . '/../config/openai.php';
require_once __DIR__ . '/../utils/LogUtil.php';

class OpenAIService {
    private $apiKey;
    private $model;
    private $temperature;
    
    public function __construct() {
        $this->apiKey = OpenAIConfig::get('api_key');
        $this->model = OpenAIConfig::get('model');
        $this->temperature = (float) OpenAIConfig::get('temperature');
    }
    
    // public function generateContent1($prompt) {
        
    //     $version = isset($_SESSION['version']) ? $_SESSION['version'] : 'unknown';
    //     return [
    //         'content' => 'version ' . $version . ' Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus ut metus felis. Nulla lobortis velit elit, et interdum nunc euismod vel. Fusce laoreet a est in posuere. Sed egestas ut nunc eu efficitur. Donec est ex, auctor non pharetra viverra, scelerisque nec mauris. Nam metus'
    //     ];
    // }
    
    public function generateContent($prompt) {
        $url = 'https://api.openai.com/v1/chat/completions';
        
        // Log the request prompt (truncated for readability)
        $promptToLog = is_string($prompt) ? 
            (strlen($prompt) > 200 ? substr($prompt, 0, 200) . '...' : $prompt) : 
            'Structured prompt object';
        LogUtil::log('info', 'OpenAI Request - Prompt: ' . $prompt);
        
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
        
        // Log the request data structure
        LogUtil::log('info', 'OpenAI Request - Model: ' . $this->model . ', Temperature: ' . $this->temperature);
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        // Start timing the request
        $startTime = microtime(true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $requestTime = microtime(true) - $startTime;
        
        // Log timing information
        LogUtil::log('info', sprintf('OpenAI Request - Time: %.2fs, Status: %d', $requestTime, $httpCode));
        
        curl_close($ch);
        
        if ($httpCode !== 200) {
            // Log detailed error information
            $errorMessage = 'OpenAI API request failed with status ' . $httpCode . ' Response: ' . $response;
            LogUtil::log('error', $errorMessage);
            throw new Exception($errorMessage);
        }
        
        $responseData = json_decode($response, true);
        
        // Log a truncated version of the response for visibility
        if (isset($responseData['choices'][0]['message']['content'])) {
            $content = $responseData['choices'][0]['message']['content'];
            $contentToLog = strlen($content) > 200 ? substr($content, 0, 200) . '...' : $content;
            LogUtil::log('info', 'OpenAI Response - Content: ' . $contentToLog);
        }
        
        // Format response to match ContentController expectations
        return [
            'content' => $responseData['choices'][0]['message']['content']
        ];
    }
}