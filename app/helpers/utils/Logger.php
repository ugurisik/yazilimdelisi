<?php

namespace App\helpers\utils;

use App\helpers\utils\DebugBar;
use App\helpers\utils\session;
use App\helpers\utils\FileUploader;
use Core\Mysql;

class Logger
{

    private static $instance = null;
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function log($message, $type = 'info', $saveFile = false, $saveDb = false)
    {
        $m = date('Y-m-d H:i:s') . " -> " . $type . " <- " . $message;
        $oldMessages = session::getInstance()->get('debugbar');
        $oldMessages['messages'][] = $m;
        session::getInstance()->set('debugbar', $oldMessages);

        if ($saveFile) {
            $this->logToFile($m);
        }
    }

    public function logSaveFile($message)
    {
        $this->logToFile($message);
    }

    public static function logToDB($actionGuid, $actionTitle, $actionData, $actionType = 1, $actionTable)
    {
        $data = [
            'guid' => functions::getInstance()->generateGuid(),
            'actionGuid' => $actionGuid,
            'actionTitle' => $actionTitle,
            'actionData' => json_encode($actionData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'actionType' => $actionType,
            'actionTable' => $actionTable,
            'userGuid' => "-",
            'actionDate' => date('Y-m-d H:i:s'),
            'userIP' => security::getIP(),
            'userAgent' => security::getBrowser(),
            'userOS' => security::getOS(),
            'userBrowser' => security::getBrowser(),
            'userLang' => security::getLang(),
            'createdDate' => date('Y-m-d H:i:s'),
            'updatedDate' => date('Y-m-d H:i:s'),
        ];
        $db = new Mysql();
        $db->addData('user_actions', $data);
    }

    public function logToFile($message)
    {
        FileUploader::getInstance()->setLogFileData($message);
    }
}
