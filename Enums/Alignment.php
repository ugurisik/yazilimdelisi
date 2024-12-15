<?php

namespace Enums;

enum Alignment: int
{
    case LEFT = 1;
    case CENTER = 2;
    case RIGHT = 3;

    public function value(): int
    {
        return $this->value;
    }
}
