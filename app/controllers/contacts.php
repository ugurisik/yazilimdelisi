<?php

use App\helpers\utils\Security;
use App\helpers\utils\functions as func;
use Core\Mysql;
use App\helpers\utils\TimeChecker;
use App\helpers\utils\DebugBar;
use App\helpers\utils\FileUploader;
use App\helpers\utils\Logger;
use App\helpers\auth\Components;
class contacts extends Controller
{
    public function index(...$params)
    {
        $debugBar = DebugBar::getInstance();
        $debugBar->addMessage("Hello, World!", "info");
        $debugBar->addException(new Exception("An error occurred"));
        Logger::getInstance()->log("FileUploader class oluşturuldu.", 'error', true,false);
        
        $this->setData("page_title", "Contacts");
        $this->view(DEFAULT_THEME, "main", $params, false);
    }

    public function addComp(){
        Components::getInstance()->addComponents();
    }

    public function add()
    {
        $this->jsonResponse(["status" => "success", "message" => "User added successfully", "csrf_token" => Security::getCSRF()]);
    }


    public function edit(...$params)
    {
        $tt = new TimeChecker();
        $db = new Mysql();
        $db->timer = $tt;
        $tt->checkTime();


        //$datas = $db->getWithPagination("accountlist", 1, 10, ['visible'=>0], [], []);
         $datas = $db->getData("accountlist", ['visible'=>0], [], 10,[]);
     
        //$d = $db->rawQuery("SELECT * FROM accountlist");
        $tt->checkTime();
         $error = $db->getError();
       
        echo "Hata Mesajı: " . $db->getErrorMessage();
        echo "MySQL Hata Kodu: " . $error['code'];
        echo "MySQL Hata Mesajı: " . $error['mysql_error'];

        if ($db->debugMode) {
            echo "Çalıştırılan Sorgu: " . $error['query'] . "<br>";
        }

        $logs = $db->getQueryLog();
        foreach ($logs as $log) {
            print_r($log);
        }

        // print_r($datas);
    }
}
