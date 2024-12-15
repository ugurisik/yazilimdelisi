<?php

namespace App\records;

use Core\ObjectCore;
use Core\Record;
use Core\RecordProperties;
use Helpers\Field;
use Helpers\SimpleComboAdapter;
use App\models\MapCountry;
use Enums\Alignment;
use Enums\DataType;
use Enums\DisplayType;
use Helpers\FieldList;

class Country extends MapCountry implements Record
{
    private static $fields;
    public static $isEmpty = true;

    public function __construct($guid = null)
    {
        self::$fields = new FieldList();
        self::initialize();
        if ($guid != null) {
            self::$isEmpty = ObjectCore::load($this, $guid);
        }
    }

    private function initialize(): void
    {
        if (self::$fields->getCount() == 0) {
            self::$fields = new FieldList();

            $f = new Field();
            $f->name = "guid";
            $f->defaultValue = "";
            $f->dataType = DataType::STRING;
            self::$fields->add($f);

            $f = new Field();
            $f->name = "commonName";
            $f->defaultValue = "";
            $f->dataType = DataType::STRING;
            $f->searchable = true;
            $f->sortable = true;
            $f->displayType = DisplayType::TEXT;
            $f->isEditableColumn = true;
            $f->isColumn = true;
            $f->isEditableInput = true;
            $f->focusOrder = 1;
            $f->isRequired = true;
            $f->placeHolder = "Enter common name";
            $f->label = "Common Name";
            self::$fields->add($f);

            $f = new Field();
            $f->name = "nativeName";
            $f->defaultValue = "";
            $f->dataType = DataType::STRING;
            $f->displayType = DisplayType::TEXT;
            $f->isColumn = true;
            $f->isEditableInput = true;
            $f->isRequired = true;
            $f->focusOrder = 2;
            $f->maxValue = 100;
            $f->minValue = 0;
            $f->placeHolder = "Enter native name";
            $f->label = "Native Name";
            $f->searchable = true;
            $f->sortable = true;
            self::$fields->add($f);

            $f = new Field();
            $f->name = "iso2";
            $f->defaultValue = "";
            $f->dataType = DataType::STRING;
            $f->displayType = DisplayType::TEXT;
            $f->isColumn = true;
            $f->isEditableInput = true;
            $f->isRequired = true;
            $f->placeHolder = "Enter ISO2";
            $f->label = "ISO2";
            $f->focusOrder = 1;
            $f->maxLength = 2;
            $f->searchable = true;
            $f->sortable = true;
            self::$fields->add($f);

            $f = new Field();
            $f->name = "iso3";
            $f->defaultValue = "";
            $f->dataType = DataType::STRING;
            $f->displayType = DisplayType::TEXT;
            $f->isColumn = true;
            $f->isEditableInput = true;
            $f->isRequired = true;
            $f->placeHolder = "Enter ISO3";
            $f->label = "ISO3";
            $f->maxLength = 3;
            $f->searchable = true;
            $f->sortable = true;
            self::$fields->add($f);

            $f = new Field();
            $f->name = "currency";
            $f->defaultValue = "";
            $f->dataType = DataType::STRING;
            $f->displayType = DisplayType::TEXT;
            $f->isColumn = true;
            $f->isEditableInput = true;
            $f->isRequired = true;
            $f->placeHolder = "Enter currency";
            $f->label = "Currency";
            $f->searchable = true;
            $f->sortable = true;
            self::$fields->add($f);

            $f = new Field();
            $f->name = "phoneCode";
            $f->defaultValue = "";
            $f->dataType = DataType::STRING;
            $f->displayType = DisplayType::TEXT;
            $f->isColumn = true;
            $f->isEditableInput = true;
            $f->isRequired = true;
            $f->placeHolder = "Enter phone code";
            $f->label = "Phone Code";
            $f->maxLength = 12;
            $f->searchable = true;
            $f->sortable = true;
            self::$fields->add($f);

            $f = new Field();
            $f->name = "capital";
            $f->defaultValue = "";
            $f->dataType = DataType::STRING;
            $f->displayType = DisplayType::TEXT;
            $f->isColumn = true;
            $f->isEditableInput = true;
            $f->isRequired = true;
            $f->placeHolder = "Enter capital";
            $f->label = "Capital";
            $f->searchable = true;
            $f->sortable = true;
            self::$fields->add($f);

            $f = new Field();
            $f->name = "region";
            $f->defaultValue = "";
            $f->dataType = DataType::STRING;
            $f->displayType = DisplayType::TEXT;
            $f->isColumn = true;
            $f->isEditableInput = true;
            $f->isRequired = true;
            $f->placeHolder = "Enter region";
            $f->label = "Region";
            $f->searchable = true;
            $f->sortable = true;
            self::$fields->add($f);

            $f = new Field();
            $f->name = "subRegion";
            $f->defaultValue = "";
            $f->dataType = DataType::STRING;
            $f->displayType = DisplayType::TEXT;
            $f->isColumn = true;
            $f->isEditableInput = true;
            $f->isRequired = true;
            $f->placeHolder = "Enter sub region";
            $f->label = "Sub Region";
            $f->searchable = true;
            $f->sortable = true;
            self::$fields->add($f);

            $f = new Field();
            $f->name = "languages";
            $f->defaultValue = "";
            $f->dataType = DataType::STRING;
            $f->displayType = DisplayType::TEXT;
            $f->isColumn = true;
            $f->isEditableInput = true;
            $f->isRequired = true;
            $f->placeHolder = "Enter languages";
            $f->label = "Languages";
            $f->searchable = true;
            $f->sortable = true;
            self::$fields->add($f);

            $f = new Field();
            $f->name = "latLng";
            $f->defaultValue = "";
            $f->dataType = DataType::STRING;
            $f->displayType = DisplayType::TEXT;
            $f->isColumn = true;
            $f->isEditableInput = true;
            $f->isRequired = true;
            $f->placeHolder = "Enter lat lng";
            $f->label = "Lat Lng";
            $f->searchable = true;
            $f->sortable = true;
            self::$fields->add($f);

            $f = new Field();
            $f->name = "status";
            $f->defaultValue = 0;
            $f->dataType = DataType::INT;
            $f->displayType = DisplayType::COMBOBOX;
            $f->isEditableInput = true;
            $f->isEditableColumn = true;
            $f->isColumn = true;
            $f->searchable = true;
            $f->sortable = true;
            $f->isRequired = true;
            $f->label = "Status";
            $f->comboAdapter = new SimpleComboAdapter('1|Aktif~0|Pasif');
            self::$fields->add($f);
        }
        ObjectCore::setFieldsToDefault($this);
    }

    public function process(): void {
        if($this->status == 1) {
        }
    }

    public function load(array $data): void
    {
        ObjectCore::copyPojoToRecord($this, $data);
    }

    public function save(): bool
    {
        return ObjectCore::save($this);
    }

    public function delete(bool $force = false): void
    {
        if($force){
            ObjectCore::delete($this);
        }else{
            $this->status = 0;
            $this->save();
        }
    }

    public function getEmpty(): bool
    {
        return self::$isEmpty;
    }

    public function disableLog(): bool
    {
        return false;
    }

    public function getField(): RecordProperties
    {
        $properties = new \Core\RecordProperties();
        $properties->title = 'Country';
        $properties->primaryKey = 'guid';
        $properties->tableName = 'country';
        $properties->fields = self::$fields;
        return $properties;
    }
}
