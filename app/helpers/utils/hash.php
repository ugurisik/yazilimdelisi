<?php

namespace App\helpers\utils;

use App\helpers\utils\HashException;
use App\helpers\utils\functions;

class hash
{
    private static $instance = null;
    private $key;
    private $cipher;
    private $options;
    private $ivLength;

    private function __construct()
    {
        $this->cipher = "AES-256-CBC";
        $this->key = defined('ENCRYPTION_KEY') ? ENCRYPTION_KEY : "ZZSAA147598237AASZ";
        $this->options = OPENSSL_RAW_DATA;
        $this->ivLength = openssl_cipher_iv_length($this->cipher);
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function encrypt($data)
    {
        try {
            $iv = random_bytes($this->ivLength);

            if (!is_string($data)) {
                $data = json_encode($data);
            }

            $encrypted = openssl_encrypt(
                $data,
                $this->cipher,
                $this->key,
                $this->options,
                $iv
            );

            if ($encrypted === false) {
                throw new HashException("Şifreleme işlemi başarısız oldu");
            }
            return base64_encode($iv . $encrypted);
        } catch (\Exception $e) {
            throw new HashException("Şifreleme hatası: " . $e->getMessage());
        }
    }

    public function decrypt($data)
    {
        try {
            $data = base64_decode($data);

            $iv = substr($data, 0, $this->ivLength);
            $encrypted = substr($data, $this->ivLength);

            $decrypted = openssl_decrypt(
                $encrypted,
                $this->cipher,
                $this->key,
                $this->options,
                $iv
            );

            if ($decrypted === false) {
                throw new HashException("Şifre çözme işlemi başarısız oldu");
            }

            if (functions::getInstance()->isJson($decrypted)) {
                return json_decode($decrypted, true);
            }

            return $decrypted;
        } catch (\Exception $e) {
            throw new HashException("Şifre çözme hatası: " . $e->getMessage());
        }
    }

    public function hashPassword($password)
    {
        return password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 3
        ]);
    }

    public function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    public function fileHash($filePath, $algorithm = 'sha256')
    {
        if (!file_exists($filePath)) {
            throw new HashException("Dosya bulunamadı: " . $filePath);
        }
        return hash_file($algorithm, $filePath);
    }

    public function hmac($data, $key = null, $algorithm = 'sha256')
    {
        $key = $key ?? $this->key;
        return hash_hmac($algorithm, $data, $key);
    }

    public function rotateKey()
    {
        return base64_encode(random_bytes(32));
    }

    public function reencrypt($data, $newKey)
    {
        $oldKey = $this->key;
        $this->key = $newKey;

        $decrypted = $this->decrypt($data);
        $reencrypted = $this->encrypt($decrypted);

        $this->key = $oldKey;
        return $reencrypted;
    }
}
