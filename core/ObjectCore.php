<?php

namespace Core;

use App\helpers\utils\functions;
use App\helpers\utils\Logger;
use Core\Record;
use Core\Mysql;
use Helpers\DynamicComboAdapter\DynamicComboAdapter;
use Helpers\ListResult;

class ObjectCore
{
    public static function setFieldsToDefault(Record $r)
    {
        $fields = $r->getField();
        $fieldList = $fields->fields;
        $size = $fieldList->getCount();

        for ($i = 0; $i < $size; $i++) {
            $field = $fieldList->get($i);
            try {
                self::setFieldValue($r, $field->name, $field->defaultValue);
            } catch (\Throwable $th) {
                Logger::getInstance()->log("setFieldsToDefault " + $th->getMessage(), "ERROR", true);
            }
        }
    }

    public static function setFieldValue(Record $r, $field, $value)
    {
        try {
            $className = get_class($r);
            $property = new \ReflectionProperty($className, $field);
            $property->setAccessible(true);
            $property->setValue($r, $value);
        } catch (\ReflectionException $e) {
            Logger::getInstance()->log("setFieldValue Reflection Error: " . $e->getMessage(), "ERROR", true);
        } catch (\Throwable $th) {
            Logger::getInstance()->log("setFieldValue " + $th->getMessage(), "ERROR", true);
        }
    }

    public static function getFieldValue(Record $r, $field)
    {
        try {
            $className = get_class($r);
            $property = new \ReflectionProperty($className, $field);
            $property->setAccessible(true);
            return $property->getValue($r);
        } catch (\ReflectionException $e) {
            Logger::getInstance()->log("getFieldValue Reflection Error: " . $e->getMessage(), "ERROR", true);
        } catch (\Throwable $th) {
            Logger::getInstance()->log("getFieldValue " + $th->getMessage(), "ERROR", true);
        }
        return null;
    }

    public static function checkField(Record $r, $field)
    {
        $className = get_class($r);
        try {
            $property = new \ReflectionProperty($className, $field);
            return true;
        } catch (\ReflectionException $e) {
            return false;
        }
    }


    public static function load(Record $r, $guid): bool
    {
        $tableName = $r->getField()->tableName;
        $primaryKey = $r->getField()->primaryKey;
        $db = new Mysql();
        $sql = "SELECT * FROM $tableName WHERE $primaryKey = ?";
        $data = $db->rawQuery($sql, [$guid]);
        if (empty($data)) {
            return true;
        }
        $r->load($data[0]);
        $r->process();
        return false;
    }

    public static function copyPojoToRecord(Record $r, array $data)
    {
        foreach ($data as $key => $value) {
            self::setFieldValue($r, $key, $value);
        }
    }

    public static function copyRecordToPojo(Record $r)
    {

        $fields = $r->getField()->fields;
        $size = $fields->getCount();
        for ($i = 0; $i < $size; $i++) {
            $field = $fields->get($i);
            $data[$field->name] = self::getFieldValue($r, $field->name);
        }
        return $data;
    }

    public static function saveAll(array $records)
    {
        foreach ($records as $record) {
            if ($record instanceof Record) {
                self::save($record);
            } else {
                Logger::getInstance()->log("Invalid object in records array. Expected Record instance.", "ERROR", true);
            }
        }
    }

    public static function save(Record $r): bool
    {
        try {
            $r->process();
            $pk = $r->getField()->primaryKey;
            $isNew = false;

            $guid = self::getFieldValue($r, $pk);
            if ($guid == null || empty($guid)) {
                $isNew = true;
                $guid = functions::getInstance()->generateGuid();
                self::setFieldValue($r, $pk, $guid);
                if (self::checkField($r, 'createdDate') && self::getFieldValue($r, 'createdDate') == null) {
                    self::setFieldValue($r, 'createdDate', date('Y-m-d H:i:s'));
                }

                if (self::checkField($r, 'userGuid') && self::getFieldValue($r, 'userGuid') == null) {
                    self::setFieldValue($r, 'userGuid', "");
                }
            }

            if (self::checkField($r, 'updatedDate') && self::getFieldValue($r, 'updatedDate') == null) {
                self::setFieldValue($r, 'updatedDate', date('Y-m-d H:i:s'));
            }


            $data = self::copyRecordToPojo($r);
            $db = new Mysql();
            if ($isNew) {
                $result = $db->addData($r->getField()->tableName, $data);
            } else {
                $result =  $db->updateData($r->getField()->tableName, $data, [$pk => $guid]);
            }

            if ($result && $r->disableLog() == false) {
                Logger::logToDB($guid, $isNew ? 'Yeni Kayıt' : 'Kayıt Güncelleme', $data, $isNew ? 1 : 2, $r->getField()->tableName);
            }
        } catch (\Throwable $th) {
            return false;
            Logger::getInstance()->log("Save Error " + $th->getMessage(), "ERROR", true);
        }
        return true;
    }

    public static function delete(Record $r): bool
    {
        try {
            $guid = self::getFieldValue($r, $r->getField()->primaryKey);
            $data = self::copyRecordToPojo($r);
            $db = new Mysql();
            $result = $db->deleteData($r->getField()->tableName, [$r->getField()->primaryKey => $guid]);
            if ($result && $r->disableLog() == false) {
                Logger::logToDB($guid, 'Kayıt Silme', $data, 3, $r->getField()->tableName);
            }
        } catch (\Throwable $th) {
            return false;
            Logger::getInstance()->log("Delete Error " + $th->getMessage(), "ERROR", true);
        }
        return true;
    }

    public static function deleteAll(array $records)
    {
        foreach ($records as $record) {
            if ($record instanceof Record) {
                self::delete($record);
            } else {
                Logger::getInstance()->log("Invalid object in records array. Expected Record instance.", "ERROR", true);
            }
        }
    }


    public static function list(Record $r, array $params): ListResult
    {
        $db = new Mysql();
        $db->resetQuery();
        $fields = $r->getField()->fields;
        $size = $fields->getCount();
        $fieldNames = [];
        for ($i = 0; $i < $size; $i++) {
            $field = $fields->get($i);
            $fieldNames[] = $field->name;
        }

        $listResult = new ListResult();


        $searchParam = $params['@search'];
        $sortParam = $params['@sort'];
        $pageParam = $params['@page'];


        if ($searchParam != null && empty($searchParam) == false) {
            $searchParams = explode(";", $searchParam);
            foreach ($searchParams as $searchParam) {
                $searchParam = explode(":", $searchParam);
                $field = $searchParam[0];
                $operator = $searchParam[1];
                $value = $searchParam[2];
                if (array_search($field, $fieldNames) !== false) {
                    $field = $fields->getByName($field);
                    if ($field->searchable == true) {
                        if ($field->comboAdapter != null && $field->record != null) {
                            //$fieldRecord = clone $field->record;
                            $fieldRecord = $field->record;
                            $searchField = $field->searchField;
                            $searchFieldNames = [];
                            $fieldRecordFieldNames = $fieldRecord->getField()->fields->fieldNames;
                            foreach ($searchField as $searchFieldName) {
                                if (array_search($searchFieldName, $fieldRecordFieldNames) !== false) {
                                    $searchFieldNames[] = $searchFieldName;
                                }
                            }

                            foreach ($searchFieldNames as $searchFieldName) {
                                $db->where($searchFieldName, $operator, $value);
                            }

                            $datas = $db->get($fieldRecord->getField()->tableName);
                            foreach ($datas as $data) {
                               $pk = $data[$fieldRecord->getField()->primaryKey];
                               $db->where($field->name, $operator, $pk);
                            }
                        } else {
                            $db->where($field->name, $operator, $value);
                        }
                    }
                }
            }
        }

        if($sortParam != null && empty($sortParam) == false) {
            $sortParam = explode(":", $sortParam);
            $field = $sortParam[0];
            $order = $sortParam[1];
            if (array_search($field, $fieldNames) !== false) {
                $field = $fields->getByName($field);
                if ($field->sortable == true) {
                    if ($field->comboAdapter != null && $field->record != null) {
                        $fieldRecord = $field->record;
                        $displayField = $field->displayField;
                        if (!empty($displayField)) {
                            $db->join(
                                $fieldRecord->getField()->tableName,
                                "{$r->getField()->tableName}.{$field->name} = {$fieldRecord->getField()->tableName}.{$fieldRecord->getField()->primaryKey}",
                                "LEFT"
                            );
                            $db->orderBy("{$fieldRecord->getField()->tableName}.$displayField", $order);
                        } else {
                            $db->orderBy($field->name, $order);
                        }
                    }else{
                        $db->orderBy($field->name, $order);
                    }
                }
            }
        }

        $limit = TABLE_DATA_COUNT;
        $offset = 0;
        if($pageParam != null && empty($pageParam) == false) {
            $page = $pageParam['page'];
            $start = $pageParam['start'];
            $limit = $pageParam['limit'];
            $offset = $start;
        }

        $datas = $db->withTotalCount()->get($r->getField()->tableName,  [$offset, $limit]);
        $listResult->count = $db->totalCount;
        $listResult->items = $datas;
        $listResult->page =  ceil($db->totalCount / $limit) ;


        return $listResult;
    }
}
