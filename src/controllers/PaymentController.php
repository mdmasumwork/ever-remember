<?php
require_once __DIR__ . '/../services/PaymentService.php';
require_once __DIR__ . '/../services/SessionService.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../middleware/RateLimitMiddleware.php';
require_once __DIR__ . '/../utils/EnvUtil.php';
require_once __DIR__ . '/../includes/functions.php';  // Added to use the shared function

class PaymentController {
    private $paymentService;
    private $rateLimitMiddleware;
    private $sessionService;

    public function __construct() {
        $this->paymentService = new PaymentService();
        $this->rateLimitMiddleware = new RateLimitMiddleware();
        $this->sessionService = new SessionService();
    }

    public function createPaymentIntent() {
        try {
            // Apply rate limiting
            $this->rateLimitMiddleware->handle('payment');
            
            // Get message type from session
            $messageType = strtolower($_SESSION['form_data']['messageType'] ?? 'condolence message');
            
            // Use the shared function to determine price
            $price = getPriceByMessageType($messageType);
            
            // Create payment intent with the appropriate price
            $paymentIntent = $this->paymentService->createPaymentIntent($price);
            
            return [
                'success' => true,
                'clientSecret' => $paymentIntent->client_secret
            ];
        } catch (RateLimitExceededException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
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
            $userName = $_SESSION['form_data']['firstPersonName'] ?? '-';
            $userEmail = $_SESSION['form_data']['email'] ?? '-';
            
            if ($paymentResult['success']) {
                // 2. Log to database via Service
                $paymentId = $this->paymentService->logPayment($paymentResult['payment'], $userName, $userEmail);
                
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