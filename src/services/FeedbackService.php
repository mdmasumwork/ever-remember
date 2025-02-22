<?php
require_once __DIR__ . '/../config/Database.php';

class FeedbackService {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function storeFeedback($paymentId, $feedback) {
        try {

            LogUtil::log('content', '[SessionID: ' . session_id() . '][Feedback] Feedback given');

            $stmt = $this->db->prepare("
                UPDATE payments
                SET feedback = ?
                WHERE id = ?
            ");
            
            $result = $stmt->execute([$feedback, $paymentId]);
            
            if (!$result) {
                throw new Exception('Failed to store feedback');
            }
            
            return true;
        } catch (Exception $e) {
            LogUtil::log('error', 'FeedbackService:storeFeedback(): ' . $e->getMessage());
            throw $e;
        }
    }
}
