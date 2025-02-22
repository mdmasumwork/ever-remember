<?php
require_once __DIR__ . '/../config/stripe.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../../vendor/autoload.php';

class PaymentService {
    private $stripe;
    private $db;

    public function __construct() {
        \Stripe\Stripe::setApiKey(StripeConfig::get('secret_key'));
        $this->db = Database::getInstance();
    }

    public function createPaymentIntent($amount) {
        return \Stripe\PaymentIntent::create([
            'amount' => $amount * 100, // Convert to cents
            'currency' => 'usd'
        ]);
    }

    public function verifyPayment($paymentIntentId) {
        $paymentIntent = \Stripe\PaymentIntent::retrieve($paymentIntentId);
        
        if ($paymentIntent->status !== 'succeeded') {
            throw new Exception('Payment verification failed');
        }

        return [
            'success' => true,
            'payment' => $paymentIntent
        ];
    }

    public function logPayment($paymentIntent, $userName, $userEmail) {
        try {
            LogUtil::log('content', '[SessionID: ' . session_id() . '][Payment]: Successful | Payment Intent ID:' . $paymentIntent->id . ' | User: ' . $userName . ' | Email: ' . $userEmail . ' | Amount: ' . $paymentIntent->amount / 100);

            $stmt = $this->db->prepare("
                INSERT INTO payments (
                    stripe_payment_id, 
                    user_name, 
                    user_email, 
                    amount, 
                    payment_method, 
                    status
                ) VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $paymentIntent->id,
                $userName,
                $userEmail,
                $paymentIntent->amount / 100,
                $paymentIntent->payment_method_types[0],
                'completed'
            ]);

            return $this->db->lastInsertId(); // Return the payment ID
        } catch (Exception $e) {
            error_log("Payment logging failed: " . $e->getMessage());
            throw $e;
        }
    }
}