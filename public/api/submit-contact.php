<?php

require_once __DIR__ . '/../../src/utils/LogUtil.php';
require_once __DIR__ . '/../../src/includes/functions.php';
require_once __DIR__ . '/../../src/services/SessionService.php';
require_once __DIR__ . '/../../src/utils/SecurityHeadersUtil.php';
require_once __DIR__ . '/../../src/middleware/CSRFMiddleware.php';
require_once __DIR__ . '/../../src/middleware/RateLimitMiddleware.php';
require_once __DIR__ . '/../../src/services/SingleValidationService.php';
require_once __DIR__ . '/../../src/controllers/EmailController.php';

SecurityHeadersUtil::setHeaders('POST');
SecurityHeadersUtil::handlePreflight('POST');

$sessionService = new SessionService();
$csrf = new CSRFMiddleware();
$csrf->handle('contact');

$rateLimitMiddleware = new RateLimitMiddleware();
$rateLimitMiddleware->handle('contact_form');

$validator = new SingleValidationService();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

try {
    $postData = json_decode(file_get_contents('php://input'), true);
    
    if (!$postData) {
        $postData = $_POST;
    }
    
    // Required fields validation
    $requiredFields = ['name', 'email', 'message'];
    foreach ($requiredFields as $field) {
        if (empty($postData[$field])) {
            throw new Exception("Please fill in all required fields");
        }
    }
    
    // Validate and sanitize inputs
    $name = $validator->validateAndSanitize('firstPersonName', $postData['name']);
    $email = $validator->validateAndSanitize('email', $postData['email']);
    $company = $validator->validateAndSanitize('text', $postData['company'] ?? '');
    $phone = isset($postData['phone']) ? $validator->validateAndSanitize('text', $postData['phone']) : '';
    $message = $validator->validateAndSanitize('details', $postData['message']);
    
    // Send email using EmailController
    $emailController = new EmailController();
    $emailController->sendContactFormEmail($name, $email, $company, $phone, $message);
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Thank you for your inquiry. We will contact you shortly.'
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
