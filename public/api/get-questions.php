<?php

require_once __DIR__ . '/../../src/utils/LogUtil.php';
require_once __DIR__ . '/../../src/includes/functions.php';
require_once __DIR__ . '/../../src/services/SessionService.php';
require_once __DIR__ . '/../../src/utils/SecurityHeadersUtil.php';
require_once __DIR__ . '/../../src/middleware/RateLimitMiddleware.php';

SessionService::createSession();

SecurityHeadersUtil::setHeaders('GET');
SecurityHeadersUtil::handlePreflight('GET');

$rateLimitMiddleware = new RateLimitMiddleware();
$rateLimitMiddleware->handle('questions');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

try {
    $messageType = isset($_GET['message_type']) ? $_GET['message_type'] : null;
    
    if (!$messageType) {
        throw new Exception('Message type parameter is required');
    }
    
    $questions = [
        'condolence' => [
            'title' => 'Thank you for sharing that, and I\'m truly sorry for your loss.',
            'description' => 'To help me create a meaningful message for the card or flowers, please share any details you\'d like to include. It can be simple and heartfelt.',
            'suggestions' => [
                'If you would like to keep it simple or include a personal tribute.',
                'If you would like to express support for the recipient.',
                'If you would like to add a brief personal note, such as a memory or something meaningful about ' . $_SESSION["deceased_person_first_name"] . '.',
                'Anything else you would like to include.'
            ]
        ],
        'sympathy' => [
            'title' => 'I appreciate you sharing this with me, and I\'m so sorry for your loss.',
            'description' => 'A letter of sympathy is a personal way to offer comfort to someone who is grieving. Let\'s craft a message that truly expresses your support and care.',
            'suggestions' => [
                'Who you are writing this letter to.',
                'What their relationship was with ' . $_SESSION["deceased_person_first_name"] . '.',
                'If you would like to include a comforting memory or kind words about ' . $_SESSION["deceased_person_first_name"] . '.',
                $_SESSION["deceased_person_first_name"] . '\'s character or impact on others.',
                'Anything specific you would like to mention about ' . $_SESSION["deceased_person_first_name"] . '\' legacy or what they meant to people.'

            ]
        ],
        'eulogy' => [
            'title' => 'Thank you for entrusting me with this important tribute.',
            'description' => 'To help me craft a meaningful eulogy for ' . $_SESSION["deceased_person_first_name"] . ', I\'d like to learn more about their life journey.',
            'suggestions' => [
                'Some qualities or characteristics that best describe ' . $_SESSION["deceased_person_first_name"] . '.',
                'A meaningful story or memory that reflects ' . $_SESSION["deceased_person_first_name"] . '\'s personality.',
                $_SESSION["deceased_person_first_name"] . '\'s passions, hobbies, or contributions to others.',
                'How ' . $_SESSION["deceased_person_first_name"] . ' positively impacted the lives of their loved ones and community.',
                'If you would like to include a favorite quote, poem, or reflection.',
                'Anything else you\'d like to include.'
            ]
        ],
        'obituary' => [
            'title' => 'I\'m sorry for your loss and appreciate your trust in this important task.',
            'description' => 'To craft a respectful obituary for ' . $_SESSION["deceased_person_first_name"] . ', could you provide these essential details?',
            'suggestions' => [
                'When and where ' . $_SESSION["deceased_person_first_name"] . ' was born.',
                'When and where ' . $_SESSION["deceased_person_first_name"] . ' passed away.',
                'Summary of ' . $_SESSION["deceased_person_first_name"] . '\'s life, career, and key achievements.',
                $_SESSION["deceased_person_first_name"] . '\'s greatest passions, hobbies, or contributions to the community.',
                'If ' . $_SESSION["deceased_person_first_name"] . ' had any community involvement.',
                'If you would like to mention any surviving family members.',
                'If you would like to include details about funeral/memorial services.',
                'Anything else you\'d like to include.'
            ]
        ]
    ];
    
    // Map the message type to our internal types
    $typeMap = [
        'condolence message' => 'condolence',
        'sympathy letter' => 'sympathy',
        'eulogy' => 'eulogy',
        'obituary' => 'obituary'
    ];
    
    $type = $typeMap[$messageType] ?? 'condolence'; // Default to condolence if not found
    
    echo json_encode([
        'success' => true,
        'questions' => $questions[$type]
    ]);
    
} catch (Exception $e) {
    LogUtil::log('error', 'get-questions.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage()
    ]);
}
