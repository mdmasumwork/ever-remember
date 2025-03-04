<?php

require_once __DIR__ . '/../../src/utils/LogUtil.php';
require_once __DIR__ . '/../../src/includes/functions.php';
require_once __DIR__ . '/../../src/services/SessionService.php';
require_once __DIR__ . '/../../src/utils/SecurityHeadersUtil.php';
require_once __DIR__ . '/../../src/middleware/CSRFMiddleware.php';
require_once __DIR__ . '/../../src/middleware/RateLimitMiddleware.php';
require_once __DIR__ . '/../../src/services/PromptService.php';
require_once __DIR__ . '/../../src/services/OpenAIService.php';
require_once __DIR__ . '/../../src/services/SingleValidationService.php';

SecurityHeadersUtil::setHeaders('POST');
SecurityHeadersUtil::handlePreflight('POST');

$sessionService = new SessionService(); // Will initiate session in constructor
$csrf = new CSRFMiddleware();
$csrf->handle();

$rateLimitMiddleware = new RateLimitMiddleware();
$rateLimitMiddleware->handle('form_data');

$promptService = new PromptService();
$validator = new SingleValidationService();


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

try {
    $postData = json_decode(file_get_contents('php://input'), true);
    
    if (!$postData || !isset($postData['field_name']) || !isset($postData['value'])) {
        throw new Exception('Missing required parameters');
    }
    
    $fieldName = $postData['field_name'];
    $value = $postData['value'];
    
    // List of allowed field names to store in session
    $allowedFields = [
        'firstPersonName',
        'email',
        'deceasedPersonName',
        'messageType',
        'relationship', 
        'details',
        'additionalInfo',
        'messageTone',
        'finalQuestionAnswer',
        'additionalInstruction'
    ];
    
    if (!in_array($fieldName, $allowedFields)) {
        throw new Exception('Invalid field name');
    }
    
    // Validate and sanitize the input value
    try {
        $sanitizedValue = $validator->validateAndSanitize($fieldName, $value);
    } catch (SingleValidationException $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false, 
            'error' => $e->getMessage()
        ]);
        exit;
    }
    
    if ($fieldName === 'additionalInstruction') {
        if (!isset($_SESSION['form_data']['additionalInstructions']) || !is_array($_SESSION['form_data']['additionalInstructions'])) {
            $_SESSION['form_data']['additionalInstructions'] = [];
        }
        $_SESSION['form_data']['additionalInstructions'][] = $sanitizedValue;
    } else {
        $_SESSION['form_data'][$fieldName] = $sanitizedValue;
    }
    
    
    // Special handling for the deceased person name
    if ($fieldName === 'deceasedPersonName') {
        $prompt = $promptService->generateNameExtractionPrompt($sanitizedValue);
        
        try {
            $openAIService = new OpenAIService();
            $response = $openAIService->generateContent($prompt);
            $parsedName = json_decode($response['content'], true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Failed to parse name from OpenAI response');
            }
            
            $_SESSION['deceasedPersonFirstName'] = $parsedName['first_name'] ?? '';
            $_SESSION['deceasedPersonMiddleName'] = $parsedName['middle_name'] ?? '';    
            $_SESSION['deceasedPersonLastName'] = $parsedName['last_name'] ?? '';
            $_SESSION['deceasedPersonFullName'] = trim(
                ($_SESSION['deceasedPersonFirstName'] ?? '') .
                ($_SESSION['deceasedPersonMiddleName'] ? ' ' . $_SESSION['deceasedPersonMiddleName'] : '') .
                ($_SESSION['deceasedPersonLastName'] ? ' ' . $_SESSION['deceasedPersonLastName'] : '')
            );
        } catch (Exception $e) {
            // LogUtil::log('error', 'Name parsing error: ' . $e->getMessage());
        }
    }
    
    // Prepare response data
    $responseData = [
        'success' => true,
        'message' => 'Data stored successfully'
    ];
    
    // Special handling for message type - include price information
    if ($fieldName === 'messageType') {
        // Use the shared function to get the price
        $price = getPriceByMessageType($sanitizedValue);
        $responseData['price'] = $price;
    }
    
    // When storing firstPersonName, include name components in the response
    if ($fieldName === 'deceasedPersonName') {
        // Simply return the existing session data components if they exist
        $responseData['deceasedPersonFirstName'] = $_SESSION['deceasedPersonFirstName'] ?? '';
        $responseData['deceasedPersonMiddleName'] = $_SESSION['deceasedPersonMiddleName'] ?? '';
        $responseData['deceasedPersonLastName'] = $_SESSION['deceasedPersonLastName'] ?? '';
        $responseData['deceasedPersonFullName'] = $_SESSION['deceasedPersonFullName'] ?? '';
    }
    
    echo json_encode($responseData);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage()
    ]);
}
