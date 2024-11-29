<?php

namespace Core;
use App\helpers\utils\TimeChecker;
class Mysql extends \MysqliDb
{
    private $lastError = null;
    private $lastErrorCode = null;
    private $lastQuery = null;
    public $debugMode = false;
    private $queryLog = [];
    private $queryStartTime = null;
    public TimeChecker $timer;

    public function __construct()
    {
        parent::__construct(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT, DB_CHARSET);
        $this->debugMode = defined('DEBUG') ? DEBUG : false;
    }

    public function get($table, $limit = null, $order = ['id' => 'DESC'])
    {
        return $this->safeExecute('SELECT', function() use ($table, $limit, $order) {
            $this->resetQuery();
            foreach ($order as $key => $value) {
                $this->orderBy($key, $value);
            }
            return parent::get($table, $limit);
        });
    }

    public function getWithPagination($table, $page = 1, $perPage = 10, $whereParams = [], $whereSigns = [], $order = ['id' => 'DESC'])
    {
        return $this->safeExecute('SELECT_PAGINATED', function() use ($table, $page, $perPage, $whereParams, $whereSigns, $order) {
            
            $this->resetQuery();
            $totalCount = $this->rawQuery('SELECT count(*) as count FROM ' . $table)[0]['count'];
            $this->resetQuery();
            foreach ($whereParams as $key => $value) {
                $sign = isset($whereSigns[$key]) ? $whereSigns[$key] : '=';
                $this->addWhere($key, $value, $sign);
            }

            foreach ($order as $key => $value) {
                $this->orderBy($key, $value);
            }

           
       
            $totalPages = ceil($totalCount / $perPage);
            $page = max(1, min($page, $totalPages));
            $offset = ($page - 1) * $perPage;

            $data = parent::get($table, [$offset, $perPage]);

            return [
                'data' => $data,
                'pagination' => [
                    'total' => $totalCount,
                    'per_page' => $perPage,
                    'current_page' => $page,
                    'total_pages' => $totalPages,
                    'has_more' => $page < $totalPages
                ]
            ];
        });
    }

    public function getData($table, $whereParams = [],  $whereSigns = [], $limit = null, $order = ['id' => 'DESC'])
    {
        return $this->safeExecute('SELECT', function() use ($table, $whereParams, $limit, $whereSigns, $order) {
            $this->resetQuery();
            foreach ($whereParams as $key => $value) {
                $sign = isset($whereSigns[$key]) ? $whereSigns[$key] : '=';
                $this->addWhere($key, $value, $sign);
            }
            foreach ($order as $key => $value) {
                $this->orderBy($key, $value);
            }
            return parent::get($table, $limit);
        });
    }

    public function getDataOrWhere($table, $whereParams = [], $limit = null, $whereSigns = [], $order = ['id' => 'DESC'])
    {
        return $this->safeExecute('SELECT', function() use ($table, $whereParams, $limit, $whereSigns, $order) {
            $this->resetQuery();
            foreach ($whereParams as $key => $value) {
                $sign = isset($whereSigns[$key]) ? $whereSigns[$key] : '=';
                $this->orWhere($key, $value, $sign);
            }
            foreach ($order as $key => $value) {
                $this->orderBy($key, $value);
            }
            return parent::get($table, $limit);
        });
    }

    public function getOneData($table, $whereParams = [], $whereSigns = [], $order = ['id' => 'DESC'])
    {
        return $this->safeExecute('SELECT_ONE', function() use ($table, $whereParams, $whereSigns, $order) {
            $this->resetQuery();
            foreach ($whereParams as $key => $value) {
                $sign = isset($whereSigns[$key]) ? $whereSigns[$key] : '=';
                $this->addWhere($key, $value, $sign);
            }
            foreach ($order as $key => $value) {
                $this->orderBy($key, $value);
            }
            return parent::getOne($table);
        });
    }

    public function addData($table, $data = [])
    {
        return $this->safeExecute('INSERT', function() use ($table, $data) {
            $this->resetQuery();
            return parent::insert($table, $data);
        });
    }

    public function updateData($table, $data = [], $whereParams = [], $whereSigns = [])
    {
        return $this->safeExecute('UPDATE', function() use ($table, $data, $whereParams, $whereSigns) {
            $this->resetQuery();
            foreach ($whereParams as $key => $value) {
                $sign = isset($whereSigns[$key]) ? $whereSigns[$key] : '=';
                $this->addWhere($key, $value, $sign);
            }
            return parent::update($table, $data);
        });
    }

    public function deleteData($table, $whereParams = [], $whereSigns = [])
    {
        return $this->safeExecute('DELETE', function() use ($table, $whereParams, $whereSigns) {
            $this->resetQuery();
            foreach ($whereParams as $key => $value) {
                $sign = isset($whereSigns[$key]) ? $whereSigns[$key] : '=';
                $this->addWhere($key, $value, $sign);
            }
            return parent::delete($table);
        });
    }

    public function rawQuery($query, $params = null)
    {
        // TODO:: Fix slow queries for all
        $this->timer->checkTime();
        return $this->safeExecute('RAW_QUERY', function() use ($query, $params) {
            $this->timer->checkTime();
            $this->resetQuery();
            $this->timer->checkTime();
            parent::rawQuery($query, $params);
            $this->timer->checkTime();
        });
    }

    public function addMultipleData($table, array $multiData)
    {
        return $this->safeExecute('INSERT_MULTIPLE', function() use ($table, $multiData) {
            $this->resetQuery();
            return $this->insertMulti($table, $multiData);
        });
    }

    public function exists($table, $whereParams = [])
    {
        return $this->safeExecute('EXISTS', function() use ($table, $whereParams) {
            $this->resetQuery();
            foreach ($whereParams as $key => $value) {
                $this->where($key, $value);
            }
            return $this->has($table);
        });
    }

    public function addWhere($key, $value, $sign = '=')
    {
        if (is_int($value)) {
            $this->where($key, (int)$value, $sign);
        } elseif (is_float($value)) {
            $this->where($key, (float)$value, $sign);
        } elseif (is_bool($value)) {
            $this->where($key, (bool)$value, $sign);
        } elseif (is_null($value)) {
            $this->where($key, null, $sign);
        } else {
            $this->where($key, (string)$value, $sign);
        }
    }

    public function hasError()
    {
        return !is_null($this->lastError);
    }

    public function getError()
    {
        return [
            'message' => $this->lastError,
            'code' => $this->lastErrorCode,
            'query' => $this->debugMode ? $this->lastQuery : null,
            'mysql_error' => $this->mysqli()->error,
            'mysql_errno' => $this->mysqli()->errno
        ];
    }

    public function getErrorMessage($errorCode = null)
    {
        $errorCode = $errorCode ?? $this->lastErrorCode;
        $errorMessages = [
            1045 => 'Veritabanı bağlantı bilgileri hatalı',
            1049 => 'Veritabanı bulunamadı',
            1146 => 'Tablo bulunamadı',
            1452 => 'Yabancı anahtar kısıtlaması hatası',
            1062 => 'Benzersiz alan çakışması (Duplicate entry)',
            1451 => 'Referans bütünlüğü hatası (Cannot delete or update a parent row)',
            1064 => 'SQL sözdizimi hatası',
            1054 => 'Bilinmeyen sütun',
            1136 => 'Sütun sayısı uyuşmazlığı',
            1366 => 'Yanlış veri tipi',
            2002 => 'Sunucuya bağlanılamadı',
            2003 => 'Sunucu bulunamadı',
            2005 => 'Bilinmeyen sunucu'
        ];

        return $errorMessages[$errorCode] ?? 'Bilinmeyen hata: ' . $errorCode;
    }

    private function safeExecute($operation, $callback)
    {
        try {
            $this->lastError = null;
            $this->lastErrorCode = null;
            $this->queryStartTime = microtime(true);
            
            $result = $callback();
            
            $this->lastQuery = $this->getLastQuery();
            $this->logQuery($operation);

            if ($this->mysqli()->error) {
                $this->lastError = $this->mysqli()->error;
                $this->lastErrorCode = $this->mysqli()->errno;
                return false;
            }

            return $result;
        } catch (\Exception $e) {
            $this->lastError = $e->getMessage();
            $this->lastErrorCode = $e->getCode();
            $this->logQuery($operation);
            return false;
        }
    }

    private function logQuery($operation)
    {
        if ($this->debugMode) {
            $endTime = microtime(true);
            $duration = round(($endTime - $this->queryStartTime) * 1000, 2); 

            $this->queryLog[] = [
                'operation' => $operation,
                'query' => $this->lastQuery,
                'time' => date('Y-m-d H:i:s'),
                'duration' => $duration,
                'duration_text' => $duration . ' ms',
                'error' => $this->hasError() ? $this->getError() : null
            ];
            if ($duration > 3000) {
                error_log("SLOW QUERY [{$duration}ms] - {$operation}: {$this->lastQuery}");
            }
        }
    }

    public function getQueryLog()
    {
        return $this->queryLog;
    }

    private function resetQuery()
    {
        $this->reset();
        return $this;
    }
}