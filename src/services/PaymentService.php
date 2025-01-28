<?php
require_once __DIR__ . '/../config/stripe.php';
require_once __DIR__ . '/../../vendor/autoload.php';

class PaymentService {
    private $stripe;

    public function __construct() {
        \Stripe\Stripe::setApiKey(StripeConfig::get('secret_key'));
    }

    public function createPaymentIntent($amount) {
        return \Stripe\PaymentIntent::create([
            'amount' => $amount * 100, // Convert to cents
            'currency' => 'usd'
        ]);
    }
}