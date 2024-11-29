<?php

namespace App\helpers\utils;

use App\helpers\utils\SessionException;

class session
{
    private static $instance = null;
    private $sessionStarted = false;

    private function __construct()
    {
        $this->start();
    }
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function start()
    {
        if ($this->sessionStarted === false) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $this->sessionStarted = true;
        }
    }

    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
        return $this;
    }


    public function get($key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public function has($key)
    {
        return isset($_SESSION[$key]);
    }

    public function remove($key)
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
            return true;
        }
        return false;
    }

    public function clear()
    {
        session_unset();
        session_destroy();
    }

    public function regenerateId($deleteOldSession = true)
    {
        return session_regenerate_id($deleteOldSession);
    }

    public function isAuthenticated()
    {
        // Daha sonra kullanıcı girişi kontrolü yapılacak
        return true;
    }

    public function setUser($userData)
    {
        $this->set('user_data', $userData);
        $this->set('last_activity', time());
    }

    public function getUser()
    {
        return $this->get('user_data');
    }

    public function logout()
    {
        $this->remove('user_data');
        $this->remove('last_activity');
        $this->regenerateId();
    }

    public function checkTimeout($maxLifetime = 1800) // 30 dakika
    {
        $lastActivity = $this->get('last_activity');
        if ($lastActivity && (time() - $lastActivity > $maxLifetime)) {
            $this->logout();
            return true;
        }
        $this->set('last_activity', time());
        return false;
    }
}
