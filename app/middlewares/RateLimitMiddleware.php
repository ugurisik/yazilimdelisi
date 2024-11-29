<?php

namespace App\middlewares;

require_once __DIR__ . '/../helpers/utils/exceptions.php';

use App\helpers\utils\RateLimiter;
use App\helpers\utils\SecurityException;

class RateLimitMiddleware {
    private $rateLimiter;
    
    public function __construct() {
        $this->rateLimiter = new RateLimiter(250, 1);
    }
    
    public function handle() {
        $ip = $this->getClientIp();
        
        if ($this->rateLimiter->tooManyAttempts($ip)) {
            $seconds = $this->rateLimiter->availableIn($ip);
            
            header('X-RateLimit-Reset: ' . time() + $seconds);
            header('Retry-After: ' . $seconds);
            http_response_code(429);
            
            throw new SecurityException('Too Many Attempts. Please try again in ' . $seconds . ' seconds.', 429);
        }
        
        $attempts = $this->rateLimiter->hit($ip);
        $remaining = $this->rateLimiter->remainingAttempts($ip);
        
        header('X-RateLimit-Limit: ' . $this->rateLimiter->maxAttempts);
        header('X-RateLimit-Remaining: ' . $remaining);
    }
    
    private function getClientIp(): string {
        $headers = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];
        
        foreach ($headers as $header) {
            if (isset($_SERVER[$header]) && filter_var($_SERVER[$header], FILTER_VALIDATE_IP)) {
                return $_SERVER[$header];
            }
        }
        
        return '0.0.0.0';
    }
}
