<?php
require_once __DIR__ . '/../../src/config/stripe.php';

header('Content-Type: application/json');
echo json_encode(['publicKey' => StripeConfig::get('public_key')]);