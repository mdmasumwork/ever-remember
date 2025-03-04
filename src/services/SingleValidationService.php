<?php

class SingleValidationException extends Exception {}

class SingleValidationService {
    
    /**
     * Validate and sanitize a single field value
     * 
     * @param string $fieldName The name of the field to validate
     * @param mixed $value The value to validate and sanitize
     * @return mixed The sanitized value
     * @throws SingleValidationException If validation fails
     */
    public function validateAndSanitize($fieldName, $value) {
        // Security check for all fields
        $this->checkForMaliciousContent($value);
        
        // Field-specific validation
        switch ($fieldName) {
            case 'email':
                return $this->validateAndSanitizeEmail($value);
                
            case 'firstPersonName':
            case 'deceasedPersonName':
                return $this->validateAndSanitizeName($value);
                
            case 'messageType':
                return $this->validateAndSanitizeMessageType($value);
                
            case 'messageTone':
                return $this->validateAndSanitizeMessageTone($value);
                
            case 'relationship':
            case 'details':
            case 'additionalInfo':
            case 'finalQuestionAnswer':
            case 'additionalInstruction':
                return $this->validateAndSanitizeText($value);
                
            default:
                // Default sanitization for unknown fields
                return $this->sanitizeBasicText($value);
        }
    }
    
    /**
     * Validate and sanitize an email address
     */
    private function validateAndSanitizeEmail($email) {
        $email = trim($email);
        
        if (empty($email)) {
            throw new SingleValidationException('Email cannot be empty');
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || preg_match('/[<>]/', $email)) {
            throw new SingleValidationException('Invalid email format');
        }
        
        // Sanitize email
        return filter_var($email, FILTER_SANITIZE_EMAIL);
    }
    
    /**
     * Validate and sanitize a name
     */
    private function validateAndSanitizeName($name) {
        $name = trim($name);
        
        if (empty($name)) {
            throw new SingleValidationException('Name cannot be empty');
        }
        
        if (strlen($name) < 2) {
            throw new SingleValidationException('Name must be at least 2 characters long');
        }
        
        if (strlen($name) > 50) {
            throw new SingleValidationException('Name cannot exceed 50 characters');
        }
        
        // Check for special characters that shouldn't be in names
        // Allow alphanumeric characters plus apostrophes, hyphens, periods and spaces
        if (preg_match('/[^a-zA-Z0-9\'\-\. ]/', $name)) {
            throw new SingleValidationException('Name contains invalid characters');
        }
        
        // Sanitize name
        return $this->sanitizeBasicText($name);
    }
    
    /**
     * Validate and sanitize message type
     */
    private function validateAndSanitizeMessageType($messageType) {
        $allowedTypes = [
            'condolence message', 
            'sympathy letter', 
            'eulogy', 
            'obituary'
        ];
        
        if (!in_array($messageType, $allowedTypes)) {
            throw new SingleValidationException('Invalid message type');
        }
        
        return $messageType;
    }
    
    /**
     * Validate and sanitize message tone
     */
    private function validateAndSanitizeMessageTone($tone) {
        $allowedTones = [
            'compassionate',
            'formal',
            'poetic',
            'uplifting'
        ];
        
        if (!in_array($tone, $allowedTones)) {
            throw new SingleValidationException('Invalid message tone');
        }
        
        return $tone;
    }
    
    /**
     * Validate and sanitize free text input
     */
    private function validateAndSanitizeText($text) {
        if (is_null($text)) {
            return '';
        }
        
        if (!is_string($text)) {
            throw new SingleValidationException('Text input must be a string');
        }
        
        if (strlen($text) > 10000) {
            throw new SingleValidationException('Text exceeds maximum allowed length (10000 characters)');
        }
        
        // Sanitize text
        return $this->sanitizeBasicText($text);
    }
    
    /**
     * Sanitize basic text input with enhanced protection
     */
    private function sanitizeBasicText($input) {
        if (!is_string($input)) {
            return $input;
        }
        
        // Remove potential script tags (enhanced pattern)
        $input = preg_replace('/<\s*script\b[^>]*>.*?<\s*\/\s*script\s*>/is', '', $input);
        
        // Remove dangerous attributes from any remaining tags
        $dangerousAttributes = [
            'on\w+', 'formaction', 'href', 'xlink:href', 'src', 'data', 'action', 'content'
        ];
        $attrPattern = '/(' . implode('|', $dangerousAttributes) . ')\s*=\s*["\'][^"\']*["\']?/i';
        $input = preg_replace($attrPattern, '', $input);
        
        // Remove any HTML tags
        $input = strip_tags($input);
        
        // Remove control characters
        $input = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $input);
        
        // Trim whitespace
        $input = trim($input);
        
        // Convert special characters to HTML entities
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Check for potentially malicious content
     */
    private function checkForMaliciousContent($value) {
        if (is_string($value)) {
            // Enhanced pattern to detect more varieties of script injection
            $dangerousPatterns = [
                '/<script/i',                          // Script tags
                '/javascript:/i',                      // JavaScript protocol
                '/data:\s*[^,]*base64/i',              // Data URI with base64 encoding (more specific)
                '/vbscript:/i',                        // VBScript protocol
                '/on\w+\s*=\s*["\'][^"\']*["\']?/i',  // Event handlers with more specific pattern
                '/\beval\s*\(\s*[\'"][^\'"]*[\'"]\s*\)/i', // JavaScript eval function with string arg
                '/document\.\w+\s*=/i',                // DOM manipulation assignments
                '/(?:alert|confirm|prompt)\s*\([\'"][^\'"]*[\'"]\)/i', // JavaScript dialog functions
                '/<\s*iframe/i',                       // iframes
                '/<\s*object/i',                       // Object tags
                '/<\s*embed/i',                        // Embed tags
                '/\bURL\s*\(\s*[\'"][^\'"]*[\'"]\s*\)/i', // CSS URL function with string arg
                '/expression\s*\(/i',                  // CSS expressions
                '/<?php|<\?=/i',                       // PHP tags specifically, not question marks
                '/\bexec\s*\(/i'                       // exec function calls
            ];
            
            foreach ($dangerousPatterns as $pattern) {
                if (preg_match($pattern, $value)) {
                    throw new SingleValidationException('Potentially harmful content detected');
                }
            }
            
            if (strlen($value) > 10000) {
                throw new SingleValidationException('Input exceeds maximum allowed length');
            }
        }
    }
}

?>