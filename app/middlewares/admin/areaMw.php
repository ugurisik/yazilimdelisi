<?php

namespace App\middlewares\admin;

use App\helpers\utils\DebugBar;
use App\helpers\utils\functions;
use App\helpers\utils\Logger;
use App\helpers\utils\security;
use Core\Middleware;

class areaMw extends Middleware
{
    public function cityList()
    {
        if ($this->checkAdminAuth()) {
            $draw = $_GET['draw'] ?? 1;
            $start = $_GET['start'] ?? 0;
            $length = $_GET['length'] ?? TABLE_DATA_COUNT;
            $search = $_GET['search']['value'] ?? null;
            $order = $_GET['order'] ?? [];
            $filters = $_GET['filters'] ?? [];

            $page = floor($start / $length) + 1;
            $orderColumns = [];
            if (is_array($order)) {
                foreach ($order as $ord) {
                    if (isset($ord['column']) && isset($_GET['columns']) && isset($_GET['columns'][$ord['column']]['data'])) {
                        $columnName = $_GET['columns'][$ord['column']]['data'];
                        $orderColumns[] = [
                            'column' => $columnName,
                            'dir' => $ord['dir'] ?? 'asc'
                        ];
                    }
                }
            }

            $result = $this->db->getWithPagination2('city', $page, $length, $filters, $search, $orderColumns);
            $response = [
                'draw' => (int)$draw,
                'recordsTotal' => (int)($result['pagination']['total'] ?? 0),
                'recordsFiltered' => (int)($result['pagination']['total'] ?? 0),
                'data' => $result['data'] ?? []
            ];

            return $response;
        } else {
            return $this->jsonMessage('Yetkisiz erişim', 403);
        }
    }

    public function countryList()
    {
        if ($this->checkAdminAuth()) {
            $draw = $_GET['draw'] ?? 1;
            $start = $_GET['start'] ?? 0;
            $length = $_GET['length'] ?? TABLE_DATA_COUNT;
            $search = $_GET['search']['value'] ?? null;
            $order = $_GET['order'] ?? [];
            $filters = $_GET['filters'] ?? [];

            $page = floor($start / $length) + 1;
            $orderColumns = [];
            if (is_array($order)) {
                foreach ($order as $ord) {
                    if (isset($ord['column']) && isset($_GET['columns']) && isset($_GET['columns'][$ord['column']]['data'])) {
                        $columnName = $_GET['columns'][$ord['column']]['data'];
                        $orderColumns[] = [
                            'column' => $columnName,
                            'dir' => $ord['dir'] ?? 'asc'
                        ];
                    }
                }
            }

            $result = $this->db->getWithPagination2('country', $page, $length, $filters, $search, $orderColumns);
            $response = [
                'draw' => (int)$draw,
                'recordsTotal' => (int)($result['pagination']['total'] ?? 0),
                'recordsFiltered' => (int)($result['pagination']['total'] ?? 0),
                'data' => $result['data'] ?? []
            ];

            return $response;
        } else {
            return $this->jsonMessage('Yetkisiz erişim', 403);
        }
    }

    public function zoneList()
    {
        if ($this->checkAdminAuth()) {
            $draw = $_GET['draw'] ?? 1;
            $start = $_GET['start'] ?? 0;
            $length = $_GET['length'] ?? TABLE_DATA_COUNT;
            $search = $_GET['search']['value'] ?? null;
            $order = $_GET['order'] ?? [];
            $filters = $_GET['filters'] ?? [];

            $page = floor($start / $length) + 1;
            $orderColumns = [];
            if (is_array($order)) {
                foreach ($order as $ord) {
                    if (isset($ord['column']) && isset($_GET['columns']) && isset($_GET['columns'][$ord['column']]['data'])) {
                        $columnName = $_GET['columns'][$ord['column']]['data'];
                        $orderColumns[] = [
                            'column' => $columnName,
                            'dir' => $ord['dir'] ?? 'asc'
                        ];
                    }
                }
            }

            $result = $this->db->getWithPagination2('zone', $page, $length, $filters, $search, $orderColumns);
            $response = [
                'draw' => (int)$draw,
                'recordsTotal' => (int)($result['pagination']['total'] ?? 0),
                'recordsFiltered' => (int)($result['pagination']['total'] ?? 0),
                'data' => $result['data'] ?? []
            ];

            return $response;
        } else {
            return $this->jsonMessage('Yetkisiz erişim', "error");
        }
    }

    public function addCountry(){
        if ($this->checkAdminAuth()) {
            $commonName = security::getPostData('commonName', null);
            $nativeName = security::getPostData('nativeName', null);
            $iso2 = security::getPostData('iso2', null);
            $iso3 = security::getPostData('iso3', null);
            $currency = security::getPostData('currency', null);
            $phoneCode = security::getPostData('phoneCode', null);
            $capital = security::getPostData('capital', null);
            $region = security::getPostData('region', null);
            $subRegion = security::getPostData('subRegion', null);
            $languages = security::getPostData('languages', null);
            $latLng = security::getPostData('latLng', null);
            $status = security::getPostData('status', null);
            $data = [
                'commonName' => $commonName,
                'nativeName' => $nativeName,
                'iso2' => $iso2,
                'iso3' => $iso3,
                'currency' => $currency,
                'phoneCode' => $phoneCode,
                'capital' => $capital,
                'region' => $region,
                'subRegion' => $subRegion,
                'languages' => $languages,
                'latLng' => $latLng,
                'status' => $status,
                'guid' => functions::getInstance()->generateGuid()
            ];


            $result = $this->db->addData('country', $data);
            if ($result) {
                DebugBar::getInstance()->addMessage("Başarıyla eklendi", 'success');
                return ['message' => 'Başarıyla eklendi', 'status' => 'success'];
            }else{
                Logger::getInstance()->logSaveFile($this->db->getError());
                return ['message' => $this->db->getError(), 'status' => 'error'];
            } 
        } else {
            return ['message' => 'Yetkisiz erişim', 'status' => 'error'];
        }
    }
}
