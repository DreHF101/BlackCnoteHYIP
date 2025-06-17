<?php

namespace Hyiplab\BackOffice;

class Session
{
    public function __construct()
    {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public function has($key)
    {
        $flashKey = $key . '.____flash';
        if (isset($_SESSION[$flashKey])) {
            return true;
        }
        if (isset($_SESSION[$key])) {
            return true;
        }
        return false;
    }

    public function put($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public function get($key)
    {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }
        $flashKey = $key . '.____flash';
        if (isset($_SESSION[$flashKey])) {
            $key = $flashKey;
        }
        if (isset($_SESSION[$key])) {
            $sessionValue = $_SESSION[$key];
        }
        if (isset($sessionValue)) {
            return $sessionValue;
        }
    }

    public function flash($key, $value)
    {
        $this->put($key . '.____flash', $value);
    }

    public function forget($key)
    {
        unset($_SESSION[$key]);
    }
}
