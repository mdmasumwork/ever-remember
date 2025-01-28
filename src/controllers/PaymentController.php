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
            // 1. Verify with Stripe
            $paymentIntent = \Stripe\PaymentIntent::retrieve($paymentIntentId);
            
            if ($paymentIntent->status !== 'succeeded') {
                throw new Exception('Payment verification failed');
            }

            // 2. Get payment method details
            $paymentMethod = $paymentIntent->payment_method;
            $paymentDetails = \Stripe\PaymentMethod::retrieve($paymentMethod);
            
            // 3. Log to database
            $db = Database::getInstance();
            $stmt = $db->prepare("
                INSERT INTO payments (
                    stripe_payment_id, user_name, user_email, 
                    amount, payment_method, status
                ) VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $paymentIntent->id,
                $userName,
                $userEmail,
                $paymentIntent->amount / 100,
                $paymentDetails->card->brand,
                'completed'
            ]);

            // 4. Set payment verification in session
            session_start();
            $_SESSION['payment_verified'] = true;

            return true;
        } catch (Exception $e) {
            error_log("Payment verification failed: " . $e->getMessage());
            throw $e;
        }
    }
}