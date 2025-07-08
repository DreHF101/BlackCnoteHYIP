<?php

namespace Hyiplab\Helpers;

class RateLimiter
{
    public static function tooManyAttempts(string $key, int $maxAttempts, int $decaySeconds): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $now = time();
        $attempts = $_SESSION['rate_limit'][$key]['attempts'] ?? 0;
        $expires = $_SESSION['rate_limit'][$key]['expires'] ?? 0;

        if ($now > $expires) {
            $_SESSION['rate_limit'][$key] = [
                'attempts' => 1,
                'expires' => $now + $decaySeconds
            ];
            return false;
        }

        if ($attempts >= $maxAttempts) {
            return true;
        }

        $_SESSION['rate_limit'][$key]['attempts']++;
        return false;
    }
}

if (!function_exists('rate_limit')) {
    function rate_limit($key, $maxAttempts = 5, $decaySeconds = 60) {
        return \Hyiplab\Helpers\RateLimiter::tooManyAttempts($key, $maxAttempts, $decaySeconds);
    }
} 