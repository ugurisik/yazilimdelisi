<?php

use Core\Model;

class MapCity extends Model
{
    public string $tableName = 'city';
    public int $id;
    public string $guid;
    public string $countryGuid;
    public string $cityName;
    public int $status;
}
