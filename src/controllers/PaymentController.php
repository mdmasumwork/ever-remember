<?php
require_once __DIR__ . '/../services/PaymentService.php';
require_once __DIR__ . '/../config/Database.php';

class PaymentController {
    private $paymentService;

    public function __construct() {
        $this->paymentService = new PaymentService();
    }

    public function createPaymentIntent() {
        try {
            $paymentIntent = $this->paymentService->createPaymentIntent(9.99);
            return [
                'success' => true,
                'clientSecret' => $paymentIntent->client_secret
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function verifyAndLogPayment($paymentIntentId, $userName, $userEmail) {
        try {
            // 1. Verify with Service (stateless)
            $paymentResult = $this->paymentService->verifyPayment($paymentIntentId);
            
            if ($paymentResult['success']) {
                // 2. Log to database via Service
                $paymentId = $this->paymentService->logPayment($paymentResult['payment'], $userName, $userEmail);
                
                // 3. Set session in Controller
                session_start();
                $_SESSION['payment_verified'] = true;
                $_SESSION['payment_id'] = $paymentId; // Store payment ID in session
                
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            throw $e;
        }
    }
}