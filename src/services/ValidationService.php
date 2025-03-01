<?php

require_once __DIR__ . '/SanitizerService.php';

class ValidationException extends Exception {}

class ValidationService {
    private $sanitizer;
    const TYPE_CONTENT = 'content';
    const TYPE_FEEDBACK = 'feedback';
    
    public function __construct() {
        $this->sanitizer = new SanitizerService();
    }

    public function validateAndSanitize($data, $session, $type = self::TYPE_CONTENT) {
        // Business validations
        $this->validateBusinessRules($data, $session, $type);
        
        // Sanitize data
        $sanitizedData = $this->sanitizer->sanitizeInput($data);
        
        // Domain-specific validations
        $this->validateDomainRules($sanitizedData, $session, $type);

        return $sanitizedData;
    }

    private function validateBusinessRules($data, $session, $type) {
        // Security checks always run
        $this->checkForMaliciousContent($data);
        
        if ($type === self::TYPE_CONTENT) {
            // Content-specific business rules
            if (!isset($session['version'])) {
                $this->validateRequiredFields($data);
            } else {
                if ($session['version'] >= 3) {
                    throw new ValidationException('You have reached the maximum number of versions allowed');
                }

                // if (isset($data['email']) && isset($data['firstPersonName']) && isset($data['deceasedPersonName'])) {
                //     throw new ValidationException('Invalid request type for current version');
                //     // throw new ValidationException('Something went wrong. You should ask for second or third version, but you are asking for the first version');
                // }

                if ($session['version'] >= 1 && (!isset($session['payment_verified']) || !$session['payment_verified'])) {
                    throw new ValidationException('Payment required');
                }
            }
        } elseif ($type === self::TYPE_FEEDBACK) {
            // Feedback-specific business rules
            if (empty($data['feedback'])) {
                throw new ValidationException('Feedback cannot be empty');
            }
        }
    }

    private function validateDomainRules($data, $session, $type) {
        if ($type === self::TYPE_CONTENT) {
            if (!isset($session['version'])) {
                // First request validations
                if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL) || preg_match('/[<>]/', $data['email'])) {
                    throw new ValidationException("Invalid email format");
                }

                if (strlen($data['firstPersonName']) < 2 || strlen($data['deceasedPersonName']) < 2) {
                    throw new ValidationException("Names must be at least 2 characters long");
                }

                if (strlen($data['firstPersonName']) > 50 || strlen($data['deceasedPersonName']) > 50) {
                    throw new ValidationException("Names cannot exceed 50 characters");
                }
            } else {
                // Subsequent request validations
                if (!isset($data['additionalInstruction']) || empty(trim($data['additionalInstruction']))) {
                    throw new ValidationException("Additional instruction is required");
                }
            }
        } elseif ($type === self::TYPE_FEEDBACK) {
            // Feedback-specific domain rules
            if (strlen($data['feedback']) > 1000) {
                throw new ValidationException("Feedback cannot exceed 1000 characters");
            }
        }
    }

    private function validateRequiredFields($data) {
        $requiredFields = ['email', 'firstPersonName', 'deceasedPersonName', 'messageType', 'messageTone', 'relationship', 'details'];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                throw new ValidationException("The field '{$field}' is required");
            }
        }
    }

    private function checkForMaliciousContent($data) {
        foreach ($data as $value) {
            if (is_string($value)) {
                if (preg_match('/<script|javascript:|data:|vbscript:|onload=|onerror=|onclick=/i', $value)) {
                    throw new ValidationException('Potentially harmful content detected');
                }
                
                if (strlen($value) > 10000) {
                    throw new ValidationException('Input exceeds maximum allowed length');
                }
            }
        }
        
        if (isset($_SESSION['last_submission_time']) && 
            time() - $_SESSION['last_submission_time'] < 2) {
            throw new ValidationException('Please wait before submitting again');
        }
        
        $_SESSION['last_submission_time'] = time();
    }
}
