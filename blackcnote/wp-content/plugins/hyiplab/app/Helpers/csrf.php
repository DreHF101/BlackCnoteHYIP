<?php

namespace Hyiplab\Helpers;

class Csrf
{
    public static function generateToken(): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $token = bin2hex(random_bytes(32));
        $_SESSION['_csrf_token'] = $token;
        return $token;
    }

    public static function getToken(): ?string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        return $_SESSION['_csrf_token'] ?? null;
    }

    public static function checkToken($token): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        return isset($_SESSION['_csrf_token']) && hash_equals($_SESSION['_csrf_token'], $token);
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token() {
        return \Hyiplab\Helpers\Csrf::generateToken();
    }
}

if (!function_exists('csrf_check')) {
    function csrf_check($token) {
        return \Hyiplab\Helpers\Csrf::checkToken($token);
    }
} 