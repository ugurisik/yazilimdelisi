<?php

use App\helpers\utils\security as security;
use App\helpers\utils\functions as func;
use Core\Mysql;
use App\helpers\utils\TimeChecker;

class contacts extends Controller
{
    public function index(...$params)
    {
        $this->setData("page_title", "Contacts");
        $this->view("user", "main", $params, false);
        echo "User IP: " . security::getIP() . "<br>";
        echo "User OS: " . security::getOS() . "<br>";
        echo "User Browser: " . security::getBrowser() . "<br>";
        echo "User Agent: " . security::getUserAgent() . "<br>";
        echo "User Language: " . security::getLang() . "<br>";
        $func = new func();
        echo "Random String: " . $func->generateRandomString(10) . "<br>";
    }

    public function add()
    {
        $this->jsonResponse(["status" => "success", "message" => "User added successfully", "csrf_token" => security::getCSRF()]);
    }


    public function edit(...$params)
    {
        $tt = new TimeChecker();
        $db = new Mysql();
        $db->timer = $tt;
        $tt->checkTime();
        //$datas = $db->getWithPagination("accountlist", 1, 10, ['visible'=>0], [], []);
       // $datas = $db->getData("accountlist", ['visible'=>0], [], 10,[]);
        $d = $db->rawQuery("SELECT * FROM accountlist");
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
