<?php

namespace Helpers;

use Core\Record;
use Enums\Alignment;
use Enums\DataType;
use Enums\DisplayType;
use Helpers\ComboAdapter;

class Field
{
    public string $name;
    public mixed $defaultValue;
    public bool $searchable;
    public bool $sortable;
    public bool $isNegative;
    public bool $isColumn = false;
    public bool $isEditableColumn = false;
    public bool $isEditableInput = false;
    public bool $isDisabled = false;
    public bool $isHidden = false;
    public bool $isRequired = false;
    public ?DisplayType $displayType;
    public ?DataType $dataType;
    public ?Alignment $align;
    public ?ComboAdapter $comboAdapter;
    public ?Record $record;
    public array $searchField;
    public string $displayField;
    public int $width = 220;
    public int $height = 40;
    public int $maxLength = 255;
    public int $minLength = 0;
    public int $minValue = 0;
    public int $maxValue = 9999999;
    public int $focusOrder = 0;
    public string $placeHolder = "";
    public string $label = "";
    public string $cls = "";
    public string $style = "";
    public string $tooltip = "";
    public string $mask = "";
   


    public function __construct()
    {
        $this->name = "";
        $this->defaultValue = "";
        $this->searchable = false;
        $this->sortable = false;
        $this->isNegative = false;
        $this->displayType = DisplayType::TEXT;
        $this->dataType = DataType::STRING;
        $this->align = Alignment::LEFT;
        $this->comboAdapter = null;
        $this->record = null;
        $this->searchField = [];
        $this->displayField = "";
    }
}
