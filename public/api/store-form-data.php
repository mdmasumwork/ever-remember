<?php

require_once __DIR__ . '/../../src/utils/LogUtil.php';
require_once __DIR__ . '/../../src/includes/functions.php';
require_once __DIR__ . '/../../src/services/SessionService.php';
require_once __DIR__ . '/../../src/utils/SecurityHeadersUtil.php';
require_once __DIR__ . '/../../src/middleware/CSRFMiddleware.php';
require_once __DIR__ . '/../../src/middleware/RateLimitMiddleware.php';
require_once __DIR__ . '/../../src/services/PromptService.php';
require_once __DIR__ . '/../../src/services/OpenAIService.php';

SecurityHeadersUtil::setHeaders('POST');
SecurityHeadersUtil::handlePreflight('POST');

$sessionService = new SessionService(); // Will initiate session in constructor
$csrf = new CSRFMiddleware();
$csrf->handle();

$rateLimitMiddleware = new RateLimitMiddleware();
$rateLimitMiddleware->handle('form_data');

$promptService = new PromptService();

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
        'first-person-name',
        'email',
        'deceased-person-name',
        'message-type',
        'relationship', 
        'details',
        'accomplishments',
        'message-tone',
        'final-question'
    ];
    
    if (!in_array($fieldName, $allowedFields)) {
        throw new Exception('Invalid field name');
    }
    
    // Store the value in the session with a standardized key
    $_SESSION['form_data'][$fieldName] = $value;
    
    // Special handling for the deceased person name
    if ($fieldName === 'deceased-person-name') {
        $prompt = $promptService->generateNameExtractionPrompt($value);
        
        try {
            $openAIService = new OpenAIService();
            $parsedName = json_decode($openAIService->generateContent($prompt)['content'], true);
            
            $_SESSION['deceased_person_first_name'] = $parsedName['first_name'];
            $_SESSION['deceased_person_middle_name'] = $parsedName['middle_name'];
            $_SESSION['deceased_person_last_name'] = $parsedName['last_name'];
        } catch (Exception $e) {
            LogUtil::log('error', 'Name parsing error: ' . $e->getMessage());
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Data parsed successfully'
    ]);
    
} catch (Exception $e) {
    LogUtil::log('error', 'store-form-data.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage()
    ]);
}
