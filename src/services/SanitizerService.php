<?php

// First, install HTML Purifier via Composer:
// composer require ezyang/htmlpurifier

require_once __DIR__ . '/../../vendor/autoload.php';

class SanitizerService {
    private $htmlPurifier;
    private $pdo;
    
    public function __construct(\PDO $pdo = null) {
        // Initialize HTML Purifier for complex HTML content
        $config = \HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', 'p,b,i,u,strong,em,a[href],ul,ol,li,br');
        $this->htmlPurifier = new \HTMLPurifier($config);
        
        $this->pdo = $pdo;
    }
    
    public function sanitizeInput($input, $context = 'text') {
        if (is_array($input)) {
            return array_map(function($item) use ($context) {
                return $this->sanitizeInput($item, $context);
            }, $input);
        }
        
        if (!is_string($input)) {
            return $input;
        }
        
        switch ($context) {
            case 'html':
                return $this->htmlPurifier->purify($input);
            case 'js':
                return json_encode($input);
            case 'sql':
                return $this->prepareSQLValue($input);
            default:
                return $this->sanitizeBasicText($input);
        }
    }
    
    private function sanitizeBasicText($input) {
        $input = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $input);
        $input = preg_replace('/on\w+="[^"]*"/i', '', $input); // Remove inline JS events
        $input = strip_tags($input);
        $input = trim($input);
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }
    
    
    private function prepareSQLValue($value) {
        if ($this->pdo) {
            return $this->pdo->quote($value);
        }
        throw new \Exception("Database connection is required for SQL sanitization.");
    }
    
    public function validateFile($file) {
        if (!isset($file['name']) || !isset($file['type']) || !isset($file['size'])) {
            return false;
        }
        
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'];
        $allowedMimes = [
            'image/jpeg', 'image/png',
            'application/pdf', 'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        return in_array($extension, $allowedExtensions) &&
            in_array($file['type'], $allowedMimes) &&
            $file['size'] <= 5242880;
    }
}
