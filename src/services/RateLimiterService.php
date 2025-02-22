<?php

class RateLimitExceededException extends Exception {}
class RedisExtensionMissingException extends Exception {}

class RateLimiterService {
    private $redis;
    private $prefix = 'rate_limit:';
    private $isConnected = false;
    
    public function __construct() {
        if (!extension_loaded('redis')) {
            throw new RedisExtensionMissingException(
                'Redis extension is not installed. Please install php-redis extension.'
            );
        }

        try {
            $this->redis = new Redis();
            $this->redis->connect('127.0.0.1', 6379);
            // Optional: add password if configured
            // $this->redis->auth('your_password_here');
            $this->isConnected = true;
        } catch (Exception $e) {
            error_log("Redis connection failed: " . $e->getMessage());
            // Fallback to allow requests when Redis is down
            $this->isConnected = false;
        }
    }
    
    public function checkLimit($key, $maxAttempts = 60, $window = 60) {
        // If Redis is down, allow requests but log the issue
        if (!$this->isConnected) {
            error_log("Rate limiting disabled - Redis connection failed");
            return true;
        }
        
        $key = $this->prefix . $key;
        
        $current = $this->redis->get($key);
        if (!$current) {
            $this->redis->setex($key, $window, 1);
            return true;
        }
        
        if ($current >= $maxAttempts) {
            throw new RateLimitExceededException(
                "Rate limit exceeded. Try again in " . $this->redis->ttl($key) . " seconds."
            );
        }
        
        $this->redis->incr($key);
        return true;
    }
    
    private function getIdentifier() {
        return $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
    }
}
