<?php

namespace Helpers;

use Helpers\ComboAdapter;

class SimpleComboAdapter extends ComboAdapter {

    private array $pairs = [];

    public function __construct(string $pairs) {
        $rawPairs = explode('~', $pairs);

        foreach ($rawPairs as $rawPair) {
            $values = explode('|', $rawPair);
            $pair = null;
            $pair[0] = $values[0];
            $pair[1] = $values[1];
            $this->pairs[] = $pair;
        }
    }

    public function getPairs(): array {
        return $this->pairs;
    }
}