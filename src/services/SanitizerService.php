<?php

class SanitizerService {
    public function sanitizeInput($input) {
        if (is_array($input)) {
            return array_map([$this, 'sanitizeInput'], $input);
        }
        
        if (is_string($input)) {
            // Remove HTML tags
            $input = strip_tags($input);
            
            // Convert special characters to HTML entities
            $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
            
            // Remove any null bytes
            $input = str_replace("\0", '', $input);
            
            // Remove any SQL injection attempts
            $input = $this->removeSQLInjectionPatterns($input);
            
            // Trim whitespace
            $input = trim($input);
            
            return $input;
        }
        
        return $input;
    }

    private function removeSQLInjectionPatterns($input) {
        // Remove common SQL injection patterns
        $patterns = [
            '/\b(UNION|SELECT|INSERT|UPDATE|DELETE|DROP|TRUNCATE)\b/i',
            '/[\'";\\\]/',
            '/--/',
            '/\b(OR|AND)\b\s+([\'"]?\w+[\'"?])\s*=\s*\2/i'
        ];
        
        return preg_replace($patterns, '', $input);
    }

    public function validateFileExtension($filename) {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'];
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($extension, $allowedExtensions);
    }
}
