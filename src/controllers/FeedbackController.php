<?php
require_once __DIR__ . '/../services/FeedbackService.php';

class FeedbackController {
    private $feedbackService;

    public function __construct() {
        $this->feedbackService = new FeedbackService();
    }

    public function storeFeedback() {
        try {
            session_start();
            $paymentId = $_SESSION['payment_id'] ?? null;
            
            if (!$paymentId) {
                throw new Exception('Payment ID not found');
            }

            $data = json_decode(file_get_contents('php://input'), true);
            $feedback = $data['feedback'] ?? '';

            if (empty($feedback)) {
                throw new Exception('Feedback cannot be empty');
            }

            $this->feedbackService->storeFeedback($paymentId, $feedback);

            return [
                'success' => true
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
