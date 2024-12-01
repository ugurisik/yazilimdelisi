<?php

namespace App\helpers\utils;
use App\helpers\utils\SecurityException;
use App\helpers\utils\session;
class security
{
    private static $instance = null;
    private $csrfToken;
    
    private function __construct()
    {
        $this->initCSRF();
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function initCSRF() {
        if (!session::getInstance()->has('csrf_token')) {
            $token = bin2hex(random_bytes(32));
            session::getInstance()->set('csrf_token', $token);
            setcookie('csrf_token', $token, [
                'httponly' => true,
                'secure' => true,
                'samesite' => 'Strict'
            ]);
        }
        $this->csrfToken = session::getInstance()->get('csrf_token');
    }

    public function refreshCSRFToken() {
        $token = bin2hex(random_bytes(32));
        session::getInstance()->set('csrf_token', $token);
        $this->csrfToken = $token;
        return $token;
    }

    public function getCSRFToken()
    {
        return $this->csrfToken;
    }

    static function getCSRF()
    {
        return self::getInstance()->getCSRFToken();
    }

    public function validateCSRF() {
        $safeMethods = ['GET', 'HEAD', 'OPTIONS'];
        if (!in_array($_SERVER['REQUEST_METHOD'], $safeMethods)) {
            $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
            if (!hash_equals(session::getInstance()->get('csrf_token'), $token)) {
                throw new SecurityException('CSRF token doğrulaması başarısız!');
            }
        }
    }

    public static function escape($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    public static function escapeArray($data)
    {
        if (is_array($data)) {
            return array_map([self::class, 'escapeArray'], $data);
        }
        return is_string($data) ? self::escape($data) : $data;
    }

    public static function getPostData($key = null, $default = null)
    {
        self::getInstance()->validateCSRF();
        
        if ($key === null) {
            return self::escapeArray($_POST);
        }
        
        return isset($_POST[$key]) ? self::escape($_POST[$key]) : $default;
    }

    public static function getQueryData($key = null, $default = null)
    {
        if ($key === null) {
            return array_map(function($value) {
                return self::filter($value);
            }, $_GET);
        }
        
        if (!isset($_GET[$key])) {
            return $default;
        }

        return self::filter($_GET[$key]);
    }

    public static function getIP()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public static function getOS()
    {
        $os_platform = "Bilinmeyen İşletim Sistemi";
        $os_array = array('/windows nt 10/i' => 'Windows 10', '/windows nt 6.3/i' => 'Windows 8.1', '/windows nt 6.2/i' => 'Windows 8', '/windows nt 6.1/i' => 'Windows 7', '/windows nt 6.0/i' => 'Windows Vista', '/windows nt 5.2/i' => 'Windows Server 2003/XP x64', '/windows nt 5.1/i' => 'Windows XP', '/windows xp/i' => 'Windows XP', '/windows nt 5.0/i' => 'Windows 2000', '/windows me/i' => 'Windows ME', '/win98/i' => 'Windows 98', '/win95/i' => 'Windows 95', '/win16/i' => 'Windows 3.11', '/macintosh|mac os x/i' => 'Mac OS X', '/mac_powerpc/i' => 'Mac OS 9', '/linux/i' => 'Linux', '/ubuntu/i' => 'Ubuntu', '/iphone/i' => 'iPhone', '/ipod/i' => 'iPod', '/ipad/i' => 'iPad', '/android 1/i' => 'Android 1', '/android 10/i' => 'Android 10', '/android 9/i' => 'Android 9', '/android 8/i' => 'Android 8', '/android 7/i' => 'Android 7', '/android 6/i' => 'Android 6', '/android 5/i' => 'Android 5', '/android 4/i' => 'Android 4', '/android 3/i' => 'Android 3', '/android 2/i' => 'Android 2', '/blackberry/i' => 'BlackBerry', '/webos/i' => 'Mobile');
        foreach ($os_array as $regex => $value)
            if (preg_match($regex, $_SERVER['HTTP_USER_AGENT']))
                $os_platform = $value;
        return $os_platform;
    }

    public static function getBrowser()
    {
        $browser = "Bilinmeyen Tarayıcı";
        $browser_array = array('/msie/i' => 'Internet Explorer', '/firefox/i' => 'Firefox', '/safari/i' => 'Safari', '/chrome/i' => 'Chrome', '/edge/i' => 'Edge', '/opera/i' => 'Opera', '/opr/i' => 'Opera', '/netscape/i' => 'Netscape', '/maxthon/i' => 'Maxthon', '/konqueror/i' => 'Konqueror', '/huaweibrowser/i' => 'Huawei Browser');
        foreach ($browser_array as $regex => $value)
            if (preg_match($regex, $_SERVER['HTTP_USER_AGENT']))
                $browser = $value;
        return $browser;
    }

    public static function getUserAgent()
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }

    public static function getLang()
    {
        return substr(mb_strtoupper($_SERVER['HTTP_ACCEPT_LANGUAGE'], "UTF-8"), 0, 2);
    }

    public static function filter($data) {
        if (is_array($data)) {
            return array_map([self::class, 'filter'], $data);
        }
        
        $data = preg_replace('/[\x00-\x1F\x7F]/u', '', $data);
        
        $data = self::escape($data);
        
        $data = str_replace(['union', 'select', 'insert', 'update', 'delete', 'drop', '--', '/*', '*/'], '', strtolower($data));
        
        return trim($data);
    }
}
