<?php

namespace Core;
use App\helpers\utils\security;

class Middleware
{
    public $db;
    private static $instance = null;

    public function __construct()
    {
        $this->db = new Mysql();
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function checkAdminAuth(){
        return true;
    }

    public function checkUserAuth(){
        return true;
    }

    public function jsonData($data){
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public function jsonMessage($message, $status){
        $m = [
            'status' => isset($status) ? $status : 'error',
            'message' => isset($message) ? $message : 'error',
        ];
        return $this->jsonData($m);
    }

    public function secureGetData(){

    }
}