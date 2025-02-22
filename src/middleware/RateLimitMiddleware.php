<?php

require_once __DIR__ . '/../services/RateLimiterService.php';

class RateLimitMiddleware {
    private $rateLimiter;
    
    public function __construct() {
        $this->rateLimiter = new RateLimiterService();
    }
    
    public function handle($route) {
        $limits = [
            'content' => ['attempts' => 20, 'window' => 60],    // 20 requests per minute
            'payment' => ['attempts' => 10, 'window' => 60],    // 10 requests per minute
            'feedback' => ['attempts' => 10, 'window' => 60],    // 5 requests per minute
            'session' => ['attempts' => 30, 'window' => 60],    // 30 requests per minute
            'stripe' => ['attempts' => 30, 'window' => 60],     // 30 requests per minute
            'index' => ['attempts' => 30, 'window' => 60],     // 30 requests per minute
            'email' => ['attempts' => 5, 'window' => 60]        // 5 requests per minute
        ];
        
        if (!isset($limits[$route])) {
            return true;
        }
        
        $identifier = $this->getIdentifier() . ':' . $route;
        try {
            return $this->rateLimiter->checkLimit(
                $identifier,
                $limits[$route]['attempts'],
                $limits[$route]['window']
            );
        } catch (RateLimitExceededException $e) {
            http_response_code(429); // Too Many Requests
            echo json_encode([
                'error' => true,
                'message' => 'Rate limit exceeded. Please try again later.'
            ]);
            exit();
        }
    }
    
    private function getIdentifier() {
        // Use X-Forwarded-For if behind proxy, otherwise use REMOTE_ADDR
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
        $ip = explode(',', $ip)[0]; // Take the first valid IP in case of multiple

        return $ip; // No user ID since there's no login system
    }
}
