<?php
require_once __DIR__ . '/../services/FeedbackService.php';
require_once __DIR__ . '/../services/ValidationService.php';
require_once __DIR__ . '/../middleware/CSRFMiddleware.php';
require_once __DIR__ . '/../services/SessionService.php';

class FeedbackController {
    private $feedbackService;
    private $validationService;
    private $rateLimitMiddleware;
    private $csrfMiddleware;
    private $sessionService;

    public function __construct() {
        $this->feedbackService = new FeedbackService();
        $this->validationService = new ValidationService();
        $this->csrfMiddleware = new CSRFMiddleware();
        $this->sessionService = new SessionService();
    }

    public function storeFeedback() {
        try {
            $this->csrfMiddleware->handle();

            $paymentId = $_SESSION['payment_id'] ?? null;
            
            if (!$paymentId) {
                throw new Exception('Payment ID not found');
            }

            $data = json_decode(file_get_contents('php://input'), true);
            
            // Validate and sanitize the feedback data
            // Specify validation type as feedback
            $sanitizedData = $this->validationService->validateAndSanitize(
                $data, 
                $_SESSION, 
                ValidationService::TYPE_FEEDBACK
            );
            $feedback = $sanitizedData['feedback'] ?? '';

            if (empty($feedback)) {
                throw new Exception('Feedback cannot be empty');
            }

            $this->feedbackService->storeFeedback($paymentId, $feedback);

            return [
                'success' => true
            ];
        } catch (RateLimitExceededException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
