<?php

namespace Helpers\DynamicComboAdapter;

use Core\Record;
use Helpers\ComboAdapter;
use Core\Mysql;

class DynamicComboAdapter extends ComboAdapter
{
    private array $pairs = [];
    private Record $record;
    public string $displayField;
    private string $orderField;

    public function __construct(Record $record, string $displayField, string $orderField = null) {
        $this->record = $record;
        $this->displayField = $displayField;
        $this->orderField = $orderField;
    }
    
    public function getPairs(): array {
        if($this->pairs == []) {
            $db = new Mysql();
            $tableName = $this->record->getField()->tableName; 
            if($this->orderField != null) {
                $db->orderBy($this->orderField);
            }else{
                $db->orderBy($this->displayField);
            }
            $db->where("status",0,"<>");
            $r = $db->get($tableName);
            foreach($r as $row) {
                $this->pairs[$row[$this->record->getField()->primaryKey]] = $row[$this->displayField];
            }
        }
        return $this->pairs;
    }

    public function getRecord(): Record {
        return $this->record;
    }

    public function resetPairs(): void {
        $this->pairs = [];
    }
}