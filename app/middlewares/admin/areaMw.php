<?php

namespace App\middlewares\admin;

use App\helpers\utils\DebugBar;
use App\helpers\utils\functions;
use App\helpers\utils\Logger;
use App\helpers\utils\security;
use App\helpers\utils\session;
use App\records\Country;
use Core\Middleware;
use Core\ObjectCore;

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

    public function saveCountry(){
        if ($this->checkAdminAuth()) {
            $country = new Country();
            $fields = $country->getField()->fields;
            $fieldNames = $fields->fieldNames;
            $data = [];
            foreach ($fieldNames as $fieldName) {
                $data[$fieldName] = security::getPostData($fieldName, null);
            }
            ObjectCore::copyPojoToRecord($country, $data);
            if($country->save()){
                return ['message' => 'Başarıyla kaydedildi', 'status' => 'success'];
            }else{
                return ['message' => 'Kaydedilemedi', 'status' => 'error'];
            }
        }
    }

    public function deleteCountry()
    {
        if ($this->checkAdminAuth()) {
            $deleted = false;
            foreach (security::getPostData()['items'] as $guid) {
                $country = $this->db->getOneData('country', ['guid' => $guid]);
                $country = new Country($guid);
                if(!$country->getEmpty()){
                    $country->delete(false);
                    $deleted = true;
                }
            }
            if ($deleted) {
                return ['message' => 'Başarıyla silindi', 'status' => 'success'];
            } else {
                return ['message' => 'İşlem esnasında bir hata oluştu', 'status' => 'error'];
            }
        } else {
            return ['message' => 'Yetkisiz erişim', 'status' => 'error'];
        }
    }

    public function getCountry($guid)
    {
        if ($this->checkAdminAuth()) {
            $country = $this->db->getOneData('country', ['guid' => $guid]);
            $country = new Country($guid);
            if ($country->getEmpty()) {
                return ['message' => 'Kayıt bulunamadı', 'status' => 'error'];
            }else{
                return [
                    'data' => $country,
                    'message' => 'Başarıyla getirildi',
                    'status' => 'success'
                ];
            }
        } else {
            return ['message' => 'Yetkisiz erişim', 'status' => 'error'];
        }
    }
}
