<?php

namespace App\helpers\utils;

class functions
{

    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getFileExtension($filename)
    {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }

    public function formatFileSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    public function timeAgo($datetime)
    {
        $time = strtotime($datetime);
        $now = time();
        $diff = $now - $time;

        if ($diff < 60) {
            return 'Az önce';
        } elseif ($diff < 3600) {
            return floor($diff / 60) . ' dakika önce';
        } elseif ($diff < 86400) {
            return floor($diff / 3600) . ' saat önce';
        } elseif ($diff < 604800) {
            return floor($diff / 86400) . ' gün önce';
        } else {
            return date('d.m.Y H:i', $time);
        }
    }

    public function formatDate($date, $format = 'd.m.Y')
    {
        return date($format, strtotime($date));
    }

    public function generateGuid()
    {
        return sprintf(
            '%04X%04X-%04X-%04X-%04X-%04X%04X%04X',
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(0, 4095),
            bindec(substr_replace(sprintf('%016b', mt_rand(0, 65535)), '01', 6, 2)),
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(0, 65535)
        );
    }

    public function generateRandomString($length = 10)
    {
        return bin2hex(random_bytes(($length - ($length % 2)) / 2));
    }

    public function generateRandomNumber($length = 10)
    {
        return substr(str_shuffle(str_repeat('0123456789', ceil($length / 10))), 0, $length);
    }

    public function generateRandom($length = 10)
    {
        return substr(str_shuffle(str_repeat('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / 36))), 0, $length);
    }


    public function shortenText($text, $chars = 25)
    {
        if (strlen($text) <= $chars) return $text;

        $text = substr($text, 0, $chars);
        $text = substr($text, 0, strrpos($text, ' '));
        return $text . '...';
    }

    public function shortenWords($text, $words = 25)
    {
        $textArray = explode(' ', $text);
        if (count($textArray) <= $words) return $text;

        return implode(' ', array_slice($textArray, 0, $words)) . '...';
    }


    public function TrToEn($text)
    {
        $text = trim($text);
        $search = array('À', 'È', 'Ð', 'Ø', 'ß', 'à', 'è', 'ð', 'ø', 'ÿ', '©', 'Α', 'Ι', 'Ρ', 'Ά', 'Ϋ', 'α', 'ι', 'ρ', 'ά', 'ϊ', 'Ş', 'ş', 'А', 'З', 'П', 'Ч', 'Я', 'а', 'з', 'п', 'ч', 'я', 'Є', 'є', 'Č', 'Ž', 'č', 'ž', 'Ą', 'Ż', 'ą', 'ż', 'Ā', 'Š', 'ā', 'š', 'Á', 'É', 'Ñ', 'Ù', 'á', 'é', 'ñ', 'ù', 'Β', 'Κ', 'Σ', 'Έ', 'β', 'κ', 'σ', 'έ', 'ΰ', 'İ', 'ı', 'Б', 'И', 'Р', 'Ш', 'б', 'и', 'р', 'ш', 'І', 'і', 'Ď', 'ď', 'Ć', 'ć', 'Č', 'Ū', 'č', 'ū', 'Â', 'Ê', 'Ò', 'Ú', 'â', 'ê', 'ò', 'ú', 'Γ', 'Λ', 'Τ', 'Ί', 'γ', 'λ', 'τ', 'ί', 'ϋ', 'Ç', 'ç', 'В', 'Й', 'С', 'Щ', 'в', 'й', 'с', 'щ', 'Ї', 'ї', 'Ě', 'ě', 'Ę', 'ę', 'Ē', 'Ž', 'ē', 'ž', 'Ã', 'Ë', 'Ó', 'Û', 'ã', 'ë', 'ó', 'û', 'Δ', 'Μ', 'Υ', 'Ό', 'δ', 'μ', 'υ', 'ό', 'ΐ', 'Ü', 'ü', 'Г', 'К', 'Т', 'Ъ', 'г', 'к', 'т', 'ъ', 'Ґ', 'ґ', 'Ň', 'ň', 'Ł', 'ł', 'Ģ', 'ģ', 'Ä', 'Ì', 'Ô', 'Ü', 'ä', 'ì', 'ô', 'ü', 'Ε', 'Ν', 'Φ', 'Ύ', 'ε', 'ν', 'φ', 'ύ', 'Ö', 'ö', 'Д', 'Л', 'У', 'Ы', 'д', 'л', 'у', 'ы', 'Ř', 'ř', 'Ń', 'ń', 'Ī', 'ī', 'Å', 'Í', 'Õ', 'Ű', 'å', 'í', 'õ', 'ű', 'Ζ', 'Ξ', 'Χ', 'Ή', 'ζ', 'ξ', 'χ', 'ή', 'Ğ', 'ğ', 'Е', 'М', 'Ф', 'Ь', 'е', 'м', 'ф', 'ь', 'Š', 'š', 'Ó', 'ó', 'Ķ', 'ķ', 'Æ', 'Î', 'Ö', 'Ý', 'æ', 'î', 'ö', 'ý', 'Η', 'Ο', 'Ψ', 'Ώ', 'η', 'ο', 'ψ', 'ώ', 'Ё', 'Н', 'Х', 'Э', 'ё', 'н', 'х', 'э', 'Ť', 'ť', 'Ś', 'ś', 'Ļ', 'ļ', 'Ç', 'Ï', 'Ő', 'Þ', 'ç', 'ï', 'ő', 'þ', 'Θ', 'Π', 'Ω', 'Ϊ', 'θ', 'π', 'ω', 'ς', 'Ж', 'О', 'Ц', 'Ю', 'ж', 'о', 'ц', 'ю', 'Ů', 'ů', 'Ź', 'ź', 'Ņ', 'ņ', ' ', ' ', '', '/', ' ', ' ', '', '&', ',', '?');
        $replace = array('A', 'E', 'D', 'O', 'ss', 'a', 'e', 'd', 'o', 'y', '(c)', 'A', 'I', 'R', 'A', 'Y', 'a', 'i', 'r', 'a', 'i', 'S', 's', 'A', 'Z', 'P', 'Ch', 'Ya', 'a', 'z', 'p', 'ch', 'ya', 'Ye', 'ye', 'C', 'Z', 'c', 'z', 'A', 'Z', 'a', 'z', 'A', 'S', 'a', 's', 'A', 'E', 'N', 'U', 'a', 'e', 'n', 'u', 'B', 'K', 'S', 'E', 'b', 'k', 's', 'e', 'y', 'I', 'i', 'B', 'I', 'R', 'Sh', 'b', 'i', 'r', 'sh', 'I', 'i', 'D', 'd', 'C', 'c', 'C', 'u', 'c', 'u', 'A', 'E', 'O', 'U', 'a', 'e', 'o', 'u', 'G', 'L', 'T', 'I', 'g', 'l', 't', 'i', 'y', 'C', 'c', 'V', 'J', 'S', 'Sh', 'v', 'j', 's', 'sh', 'Yi', 'yi', 'E', 'e', 'e', 'e', 'E', 'Z', 'e', 'z', 'A', 'E', 'O', 'U', 'a', 'e', 'o', 'u', 'D', 'M', 'Y', 'O', 'd', 'm', 'y', 'o', 'i', 'U', 'u', 'G', 'K', 'T', '', 'g', 'k', 't', '', 'G', 'g', 'N', 'n', 'L', 'l', 'G', 'g', 'A', 'I', 'O', 'U', 'a', 'i', 'o', 'u', 'E', 'N', 'F', 'Y', 'e', 'n', 'f', 'y', 'O', 'o', 'D', 'L', 'U', 'Y', 'd', 'l', 'u', 'y', 'R', 'r', 'N', 'n', 'i', 'i', 'A', 'I', 'O', 'U', 'a', 'i', 'o', 'u', 'Z', '3', 'X', 'H', 'z', '3', 'x', 'h', 'G', 'g', 'E', 'M', 'F', '', 'e', 'm', 'f', '', 'S', 's', 'o', 'o', 'k', 'k', 'AE', 'I', 'O', 'Y', 'ae', 'i', 'o', 'y', 'H', 'O', 'PS', 'W', 'h', 'o', 'ps', 'w', 'Yo', 'N', 'H', 'E', 'yo', 'n', 'h', 'e', 'T', 't', 'S', 's', 'L', 'l', 'C', 'I', 'O', 'TH', 'c', 'i', 'o', 'th', '8', 'P', 'W', 'I', '8', 'p', 'w', 's', 'Zh', 'O', 'C', 'Yu', 'zh', 'o', 'c', 'yu', 'U', 'u', 'Z', 'z', 'N', 'n', '-', '', '', '-', '-', '', '', '', '', '');
        $new_text = str_replace($search, $replace, $text);
        return $new_text;
    }

    public function seoUrl($text)
    {
        $text = $this->TrToEn($text);
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9]/', '-', $text);
        $text = preg_replace('/-+/', "-", $text);
        $text = trim($text, '-');
        return $text;
    }

    public function lowerCase($text)
    {
        return mb_strtolower($this->TrToEn($text), 'UTF-8');
    }

    public function upperCase($text)
    {
        return mb_strtoupper($this->TrToEn($text), 'UTF-8');
    }

    public function redirect($uri, $statusCode = 302)
    {
        header('Location: ' . $uri, true, $statusCode);
        exit;
    }

    public function isEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public function isUrl($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL);
    }

    public function isPhone($phone)
    {
        return preg_match('/^[0-9]{10}$/', $phone);
    }

    public function formatMoney($amount, $currency = '₺', $decimals = 2)
    {
        return number_format($amount, $decimals, ',', '.') . ' ' . $currency;
    }

    public function maskEmail($email)
    {
        $arr = explode("@", $email);
        return substr($arr[0], 0, 2) . str_repeat('*', strlen($arr[0])-2) . "@" . $arr[1];
    }

    public function maskPhone($phone)
    {
        return substr($phone, 0, 3) . str_repeat('*', strlen($phone)-6) . substr($phone, -3);
    }

    public function jsonEncode($data)
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public function isJson($string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}
