<?php

namespace Helpers;

use Helpers\Field;

class FieldList {
    public array $fields;
    public array $fieldNames;

    public function __construct(int $count = 0) {
        $this->fields = $count > 0 ? array_fill(0, $count, null) : [];
        $this->fieldNames = $count > 0 ? array_fill(0, $count, '') : [];
    }

    public function getCount(): int {
        return count($this->fields);
    }

    public function get(int $index): ?Field {
        return $this->fields[$index] ?? null;
    }

    public function getByName(string $name): ?Field {
        $index = array_search($name, $this->fieldNames);
        return $index !== false ? $this->fields[$index] : null;
    }

    public function set(int $index, Field $field): void {
        $this->fields[$index] = $field;
        $this->fieldNames[$index] = $field->name;
    }

    public function add(Field $field): void {
        $this->fields[] = $field;
        $this->fieldNames[] = $field->name;
    }

    public function getSize(): int {
        return count($this->fields);
    }
}