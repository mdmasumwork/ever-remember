<?php

require_once __DIR__ . '/SanitizerService.php';

class ValidationException extends Exception {}

class ValidationService {
    private $sanitizer;
    
    public function __construct() {
        $this->sanitizer = new SanitizerService();
    }

    public function validateRequest($data, $session) {
        // Sanitize all input data
        $data = $this->sanitizer->sanitizeInput($data);

        // Add security checks
        $this->checkForMaliciousContent($data);
        
        // Validate JSON data
        if (!$data) {
            throw new ValidationException('Invalid input data');
        }

        // Check if it's first request or subsequent request
        if (!isset($session['version'])) {
            $this->validateFirstRequest($data);
        } else {
            // Version limit check
            if ($session['version'] >= 3) {
                throw new ValidationException('You have reached the maximum number of versions allowed');
            }

            // Validate that user isn't trying to submit first request data again
            if (isset($data['email']) && isset($data['firstPersonName']) && isset($data['deceasedPersonName'])) {
                throw new ValidationException('Something went wrong. You should ask for second or third version, but you are asking for the first version');
            }

            // Payment verification check for versions after first
            if ($session['version'] >= 1 && (!isset($session['payment_verified']) || !$session['payment_verified'])) {
                throw new ValidationException('Payment required');
            }

            $this->validateSubsequentRequest($data);
        }
    }

    private function checkForMaliciousContent($data) {
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                // Check for potential XSS patterns
                if (preg_match('/<script|javascript:|data:|vbscript:|onload=|onerror=|onclick=/i', $value)) {
                    throw new ValidationException('Potentially harmful content detected');
                }
                
                // Check for extremely long inputs
                if (strlen($value) > 10000) {
                    throw new ValidationException('Input exceeds maximum allowed length');
                }
                
                // Check for rapid-fire submissions
                if (isset($_SESSION['last_submission_time']) && 
                    time() - $_SESSION['last_submission_time'] < 2) {
                    throw new ValidationException('Please wait before submitting again');
                }
            }
        }
        
        $_SESSION['last_submission_time'] = time();
    }

    private function validateFirstRequest($data) {
        $requiredFields = ['email', 'firstPersonName', 'deceasedPersonName', 'messageType', 'messageTone', 'relationship', 'details'];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                throw new ValidationException("The field '{$field}' is required");
            }
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException("Invalid email format");
        }

        if (strlen($data['firstPersonName']) < 2 || strlen($data['deceasedPersonName']) < 2) {
            throw new ValidationException("Names must be at least 2 characters long");
        }

        // Additional checks for email
        if (preg_match('/[<>]/', $data['email'])) {
            throw new ValidationException("Invalid email format");
        }
        
        // Check for reasonable name lengths
        if (strlen($data['firstPersonName']) > 50 || strlen($data['deceasedPersonName']) > 50) {
            throw new ValidationException("Names cannot exceed 50 characters");
        }
    }

    private function validateSubsequentRequest($data) {
        if (!isset($data['additionalInstruction']) || empty(trim($data['additionalInstruction']))) {
            throw new ValidationException("Additional instruction is required");
        }

        // if (strlen($data['additionalInstruction']) < 10) {
        //     throw new ValidationException("Additional instruction must be at least 10 characters long");
        // }
    }
}
