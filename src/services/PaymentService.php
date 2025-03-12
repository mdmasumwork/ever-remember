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
        $amount = round($amount * 100); // Convert to cents and round to the nearest integer, as Stripe expects the amount in cents
        return \Stripe\PaymentIntent::create([
            'amount' => $amount,
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

    public function logPayment($paymentIntent, $userName, $userEmail, $contentType = null) {
        try {
            LogUtil::log('content', '[SessionID: ' . session_id() . '][Payment]: Successful | Payment Intent ID:' . $paymentIntent->id . ' | User: ' . $userName . ' | Email: ' . $userEmail . ' | Amount: ' . $paymentIntent->amount / 100 . ' | Type: ' . $contentType);

            // Get promo code from session if available
            $promoCode = null;
            if (isset($_SESSION['applied_promo']) && isset($_SESSION['applied_promo']['code'])) {
                $promoCode = $_SESSION['applied_promo']['code'];
            }

            $stmt = $this->db->prepare("
                INSERT INTO payments (
                    stripe_payment_id, 
                    user_name, 
                    user_email, 
                    amount, 
                    payment_method,
                    promo_code,
                    content_type,
                    status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $paymentIntent->id,
                $userName,
                $userEmail,
                $paymentIntent->amount / 100,
                $paymentIntent->payment_method_types[0],
                $promoCode,
                $contentType,
                'completed'
            ]);

            $paymentId = $this->db->lastInsertId();

            // Check if user has a 100% discount from promo code
            if (
                isset($_SESSION['applied_promo']) && 
                isset($_SESSION['applied_promo']['promo_id'])
            ) {
                // Get promo ID from session
                $promoId = $_SESSION['applied_promo']['promo_id'] ?? null;
                
                if ($promoId) {
                    // Update the usage counter in the database
                    $db = Database::getInstance();
                    $updateStmt = $this->db->prepare("
                        UPDATE promo_codes 
                        SET current_uses = current_uses + 1 
                        WHERE id = ?
                    ");
                    $updateStmt->execute([$promoId]);
                }
            }

            return $paymentId; // Return the payment ID
        } catch (Exception $e) {
            error_log("Payment logging failed: " . $e->getMessage());
            throw $e;
        }
    }
}