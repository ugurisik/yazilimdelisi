<?php


namespace Core;

use Helpers\FieldList;
interface Record {
    public function process(): void;
    public function load(array $data): void;
    public function save(): bool;
    public function delete(bool $force = false): void;
    public function disableLog(): bool;
    public function getField(): RecordProperties;
    public function getEmpty():bool;

}

class RecordProperties  {
    public $title;
    public string $primaryKey;
    public string $tableName;
    public FieldList $fields;
}
