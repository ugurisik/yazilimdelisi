<?php

namespace Core;

use App\helpers\utils\Logger;
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
    private static $instance = null;

    protected $columnToTable = [
        'packageCategoryGuid' => ['table' => 'system_package_categories', 'column' => 'guid', 'select' => 'categoryTitle'],
        'topCategoryGuid' => ['table' => 'system_package_categories', 'column' => 'guid', 'select' => 'categoryTitle'],
        'companyGuid' => ['table' => 'system_companies', 'column' => 'guid', 'select' => 'companyName'],
        'packageGuid' => ['table' => 'system_packages', 'column' => 'guid', 'select' => 'packageTitle'],
        'countryGuid' => ['table' => 'country', 'column' => 'guid', 'select' => 'nativeName'],
        'cityGuid' => ['table' => 'city', 'column' => 'guid', 'select' => 'cityName'],
        'userGuid' => ['table' => 'users', 'column' => 'guid', 'select' => 'fullName'],
        'branchGuid' => ['table' => 'branch', 'column' => 'guid', 'select' => 'branchName'],
        'topUserGuid' => ['table' => 'users', 'column' => 'guid', 'select' => 'fullName'],
        'roleGuid' => ['table' => 'roles', 'column' => 'guid', 'select' => 'roleName'],
        'taxOfficeGuid' => ['table' => 'tax_office', 'column' => 'guid', 'select' => 'officeName']
    ];

    public function __construct()
    {
        parent::__construct(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT, DB_CHARSET);
        $this->debugMode = defined('DEBUG') ? DEBUG : false;
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    public function getAll($table, $limit = null, $order = ['id' => 'DESC'])
    {
        return $this->safeExecute('SELECT', function () use ($table, $limit, $order) {
            $this->resetQuery();
            if (isset($order)) {
                foreach ($order as $key => $value) {
                    $this->orderBy($key, $value);
                }
            }

            return parent::get($table, $limit);
        });
    }



    public function getWithPagination($table, $page = 1, $perPage = 10, $whereParams = [], $whereSigns = [], $order = ['id' => 'ASC'])
    {
        return $this->safeExecute('SELECT_PAGINATED', function () use ($table, $page, $perPage, $whereParams, $whereSigns, $order) {
            $this->resetQuery();

            $whereClause = "";
            $params = [];
            if (!empty($whereParams)) {
                $conditions = [];
                foreach ($whereParams as $key => $value) {
                    $sign = isset($whereSigns[$key]) ? $whereSigns[$key] : '=';
                    $conditions[] = "`$key` $sign ?";
                    $params[] = $value;
                }
                $whereClause = "WHERE " . implode(' AND ', $conditions);
            }

            $totalCount = $this->rawQuery("SELECT COUNT(*) as count FROM `$table` $whereClause", $params)[0]['count'];

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


            foreach ($data as $key => $value) {
                foreach ($value as $key2 => $value2) {
                    if (isset($this->columnToTable[$key2])) {
                        $tableData = $this->columnToTable[$key2];
                        $table = $tableData['table'];
                        $column = $tableData['select'];
                        $data[$key][$key2] = $this->getOneData($table, ['guid' => $value2])['' . $column . ''];
                    }
                }
            }

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


    public function getWithPagination2($table, $page = 1, $length = 10, $filters = [], $search = null, $order = null)
    {
        return $this->safeExecute('RAW_QUERY', function () use ($table, $page, $length, $filters, $search, $order) {
            $offset = ($page - 1) * $length;
            $where = [];
            $params = [];
            $joins = [];
            $select = ["$table.*"];

            $tableColumns = $this->rawQuery("SHOW COLUMNS FROM $table");
            $columnNames = array_column($tableColumns, 'Field');
            
            // Logger::getInstance()->logSaveFile('Table columns: ' . json_encode($columnNames), 'info', true);

            if (!empty($this->columnToTable)) {
                foreach ($this->columnToTable as $column => $relation) {
                    if (in_array($column, $columnNames)) {
                        Logger::getInstance()->logSaveFile("Processing column: $column with relation: " . print_r($relation, true), 'info', true);
                        
                        if (strpos($column, 'Guid') !== false) {
                            $joinTable = $relation['table'];
                            $joinTableAlias = $joinTable . '_' . $column;
                            $joins[] = "LEFT JOIN {$relation['table']} AS {$joinTableAlias} ON {$table}.{$column} = {$joinTableAlias}.{$relation['column']}";
                            
                            if (isset($relation['select'])) {
                                $select[] = "{$table}.{$column} AS {$column}_raw";
                                $select[] = "COALESCE({$joinTableAlias}.{$relation['select']}, '') AS {$column}";
                            }
                        }
                    }
                }
            }

            // Logger::getInstance()->logSaveFile('Joins array: ' . json_encode($joins), 'info', true);
            // Logger::getInstance()->logSaveFile('Select array: ' . json_encode($select), 'info', true);
            // Logger::getInstance()->logSaveFile('Filters: ' . json_encode($filters), 'info', true);
            // Logger::getInstance()->logSaveFile('Search: ' . json_encode($search), 'info', true);
            // Logger::getInstance()->logSaveFile('Order: ' . json_encode($order), 'info', true);

            if (!empty($filters)) {
                foreach ($filters as $filter) {
                    if (!isset($filter['column']) || !isset($filter['operator']) || !isset($filter['value'])) {
                        continue;
                    }

                    if (!in_array($filter['column'], $columnNames)) {
                        continue;
                    }

                    $operator = $this->convertOperator($filter['operator']);
                    $value = $filter['value'];

                    $columnInfo = $this->columnToTable[$filter['column']] ?? null;
                    $searchColumn = $columnInfo ? 
                        "{$columnInfo['table']}_{$filter['column']}.{$columnInfo['select']}" : 
                        "$table.{$filter['column']}";

                    if (is_numeric($value)) {
                        $value = strpos($value, '.') !== false ? floatval($value) : intval($value);
                        $where[] = "$searchColumn {$operator} ?";
                        $params[] = $value;
                    } else {
                        switch ($operator) {
                            case 'LIKE':
                            case 'NOT LIKE':
                                $where[] = "$searchColumn $operator ?";
                                $params[] = "%{$value}%";
                                break;
                            case 'START':
                                $where[] = "$searchColumn LIKE ?";
                                $params[] = "{$value}%";
                                break;
                            case 'END':
                                $where[] = "$searchColumn LIKE ?";
                                $params[] = "%{$value}";
                                break;
                            case 'BETWEEN':
                                $dates = explode(' - ', $value);
                                if (count($dates) === 2) {
                                    $where[] = "$searchColumn BETWEEN ? AND ?";
                                    $params[] = $dates[0];
                                    $params[] = $dates[1];
                                }
                                break;
                            default:
                                $where[] = "$searchColumn {$operator} ?";
                                $params[] = $value;
                                break;
                        }
                    }
                }
            }

            $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
            // Logger::getInstance()->logSaveFile('WHERE clause: ' . $whereClause, 'info', true);

            $joinClause = implode(' ', $joins);
            // Logger::getInstance()->logSaveFile('JOIN clause: ' . $joinClause, 'info', true);

            $selectClause = implode(', ', $select);
            // Logger::getInstance()->logSaveFile('SELECT clause: ' . $selectClause, 'info', true);

            if ($search) {
                $searchWhere = [];
                $searchColumns = array_intersect($this->getSearchableColumns($table), $columnNames);
                foreach ($searchColumns as $column) {
                    $searchWhere[] = "$column LIKE ?";
                    $params[] = "%$search%";
                }
                if (!empty($searchWhere)) {
                    $whereClause .= !empty($whereClause) ? ' AND ' : 'WHERE ';
                    $whereClause .= "(" . implode(" OR ", $searchWhere) . ")";
                }
            }

            $orderBy = "";
            if ($order) {
                $orderBy = "ORDER BY ";
                $validOrderColumns = [];
                foreach ($order as $ord) {
                    if (in_array($ord['column'], $columnNames)) {
                        $validOrderColumns[] = "{$ord['column']} {$ord['dir']}";
                    }
                }
                if (!empty($validOrderColumns)) {
                    $orderBy .= implode(", ", $validOrderColumns);
                } else {
                    $orderBy = "";
                }
            }

            $countQuery = "SELECT COUNT(DISTINCT $table.id) as total FROM $table $joinClause $whereClause";
            Logger::getInstance()->logSaveFile('Count query: ' . $countQuery, 'info', true);
            
            $countParams = $params;
            
            $totalResult = empty($countParams) ? 
                $this->rawQuery($countQuery) : 
                $this->rawQuery($countQuery, $countParams);
            
            $total = is_array($totalResult) && isset($totalResult[0]['total']) ? (int)$totalResult[0]['total'] : 0;

            $query = "SELECT $selectClause FROM $table $joinClause $whereClause $orderBy LIMIT ?, ?";
            
            $params[] = (int)$offset;
            $params[] = (int)$length;

            Logger::getInstance()->logSaveFile('Final query: ' . $query, 'info', true);
            Logger::getInstance()->logSaveFile('Final params: ' . print_r($params, true), 'info', true);

            $data = empty($params) ? 
                $this->rawQuery($query) : 
                $this->rawQuery($query, $params);

            return [
                'data' => $data ?: [],
                'pagination' => [
                    'page' => (int)$page,
                    'length' => (int)$length,
                    'total' => $total
                ]
            ];
        });
    }

    private function convertOperator($operator)
    {
        $operator = urldecode($operator);

        $validOperators = ['=', '!=', '>', '>=', '<', '<=', 'LIKE', 'NOT LIKE'];
        if (in_array($operator, $validOperators)) {
            return $operator;
        }

        switch ($operator) {
            case 'START':
                return 'LIKE';
            case 'END':
                return 'LIKE';
            case 'BETWEEN':
                return 'BETWEEN';
            default:
                return '='; 
        }
    }

    private function getSearchableColumns($table)
    {
        $columns = [];
        $result = $this->query("SHOW COLUMNS FROM $table");
        foreach ($result as $column) {
            if (
                strpos(strtolower($column['Type']), 'varchar') !== false ||
                strpos(strtolower($column['Type']), 'text') !== false
            ) {
                $columns[] = $column['Field'];
            }
        }
        return $columns;
    }




    public function getData($table, $whereParams = [],  $whereSigns = [], $limit = null, $order = ['id' => 'DESC'])
    {
        return $this->safeExecute('SELECT', function () use ($table, $whereParams, $limit, $whereSigns, $order) {
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
        return $this->safeExecute('SELECT', function () use ($table, $whereParams, $limit, $whereSigns, $order) {
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
        return $this->safeExecute('SELECT_ONE', function () use ($table, $whereParams, $whereSigns, $order) {
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
        return $this->safeExecute('INSERT', function () use ($table, $data) {
            $this->resetQuery();
            return parent::insert($table, $data);
        });
    }

    public function updateData($table, $data = [], $whereParams = [], $whereSigns = [])
    {
        return $this->safeExecute('UPDATE', function () use ($table, $data, $whereParams, $whereSigns) {
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
        return $this->safeExecute('DELETE', function () use ($table, $whereParams, $whereSigns) {
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
        return $this->safeExecute('RAW_QUERY', function () use ($query, $params) {
            $this->resetQuery();
            return parent::rawQuery($query, $params);
        });
    }

    public function addMultipleData($table, array $multiData)
    {
        return $this->safeExecute('INSERT_MULTIPLE', function () use ($table, $multiData) {
            $this->resetQuery();
            return $this->insertMulti($table, $multiData);
        });
    }

    public function exists($table, $whereParams = [])
    {
        return $this->safeExecute('EXISTS', function () use ($table, $whereParams) {
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
