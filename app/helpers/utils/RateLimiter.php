<?php

namespace App\helpers\utils;

class RateLimiter {
    private $storage;
    public $maxAttempts;
    private $decayMinutes;

    public function __construct($maxAttempts = 60, $decayMinutes = 1) {
        if (!isset($_SESSION['rate_limit_storage'])) {
            $_SESSION['rate_limit_storage'] = [];
        }
        $this->storage = &$_SESSION['rate_limit_storage'];
        $this->maxAttempts = $maxAttempts;
        $this->decayMinutes = $decayMinutes;
    }

    public function hit($key) {
        $key = $this->getKey($key);
        $now = time();
        
        $this->cleanOldAttempts($key, $now);
        
        $attempts = $this->getAttempts($key);
        
        $this->storage[$key][] = $now;
        
        return count($attempts) + 1;
    }

    public function tooManyAttempts($key): bool {
        $attempts = $this->getAttempts($key);
        return count($attempts) >= $this->maxAttempts;
    }

    public function resetAttempts($key) {
        $key = $this->getKey($key);
        unset($this->storage[$key]);
    }

    public function remainingAttempts($key): int {
        return $this->maxAttempts - count($this->getAttempts($key));
    }

    public function availableIn($key): int {
        if (empty($this->storage[$this->getKey($key)])) {
            return 0;
        }

        $oldestAttempt = min($this->storage[$this->getKey($key)]);
        return max(($oldestAttempt + ($this->decayMinutes * 60)) - time(), 0);
    }

    private function getKey($key): string {
        return sha1($key);
    }

    private function cleanOldAttempts($key, $now) {
        $threshold = $now - ($this->decayMinutes * 60);
        
        if (isset($this->storage[$key])) {
            $this->storage[$key] = array_filter(
                $this->storage[$key],
                function ($timestamp) use ($threshold) {
                    return $timestamp >= $threshold;
                }
            );
        }
    }

    private function getAttempts($key): array {
        $key = $this->getKey($key);
        $now = time();
        
        $this->cleanOldAttempts($key, $now);
        
        return $this->storage[$key] ?? [];
    }
}
