<?php

namespace Enums;

enum DataType: int {
    case STRING = 1;
    case INT = 2;
    case DOUBLE = 3;
    case BOOLEAN = 4;
    case DATE = 5;
    case DATETIME = 6;

    public function value(): int {
        return $this->value;        
    }
}