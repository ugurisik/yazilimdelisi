<?php
namespace App\helpers\utils;

use App\helpers\utils\DebugBar;
use App\helpers\utils\session;
use App\helpers\utils\FileUploader;
use Core\Mysql;

class Logger {

    private static $instance = null;
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function log($message, $type = 'info', $saveFile = false, $saveDb = false) {
        $debugBar = DebugBar::getInstance();
        $m = date('Y-m-d H:i:s') . " -> ". $type. " <- " . $message;
        $debugBar->addMessage($m, $type);
        if($saveDb){
            $this->logToDB($m, $type);
        }
        if($saveFile){
            $this->logToFile($m);
        }
    }

    public function logSaveFile($message){
        $this->logToFile($message);
    }

    public function logToDB($message, $type = 'insert'){
        $user = session::getInstance()->get('user_data');
        if ($user) {
            $data = [];
            $db = new Mysql();
            $db->addData("userlog", $data);
        } 
    }

    public function logToFile($message){
        FileUploader::getInstance()->setLogFileData($message);
    }

}
