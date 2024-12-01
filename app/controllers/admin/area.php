<?php

use App\middlewares\admin\areaMw;

class area extends Controller
{

    public $area = null;

    public function __construct()
    {
        parent::__construct();
        $this->area = new areaMw();
    }

    public function index() {}

    public function countries() {
        $this->view("admin", "area/countryList", adminAuth:true, params:['page' => 'admin:countries']);
    }

    public function cities() {
        $this->view("admin", "area/cityList", adminAuth:true, params:['page' => 'admin:cities']);
    }

    public function zones() {
        $this->view("admin", "area/zoneList", adminAuth:true, params:['page' => 'admin:zones']);
    }

    public function list($param)
    {
        if (isset($param) && $param == "country") {
            $list = $this->area->countryList($param);
            $this->jsonResponse($list);
        }else if (isset($param) && $param == "city") {
            $list = $this->area->cityList($param);
            $this->jsonResponse($list);
        }else if (isset($param) && $param == "zone") {
            $list = $this->area->zoneList($param);
            $this->jsonResponse($list);
        } else {
            $message = [
                'status' => 'error',
                'message' => 'Parameters not found',
            ];
            $this->jsonResponse($message, 404);
        }
    }

    public function add($param){
        if (isset($param) && $param == "country") {
            $this->jsonResponse($this->area->addCountry(), 200);
        }else if (isset($param) && $param == "city") {
            
        }else if (isset($param) && $param == "zone") {
            
        }
    }
}
