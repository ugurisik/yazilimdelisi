<?php

namespace Enums;

enum DisplayType: int
{
    case TEXT = 1;
    case NUMBER = 2;
    case DATE = 3;
    case TIME = 4;
    case DATETIME = 5;
    case CHECKBOX = 6;
    case RADIO = 7;
    case COMBOBOX = 8;
    case TEXTAREA = 9;
    case COLORPICKER = 10;
    case PASSWORD = 11;
    case FILE = 12;
    case HIDDEN = 13;
    case RANGE = 14;
    case EMAIL = 15;
    case LABEL = 16;
    case MULTISELECT = 17;

    public function value(): int
    {
        return $this->value;
    }
}
