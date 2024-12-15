<?php

use Core\Model;

class MapZone extends Model
{
    public string $tableName = 'zone';
    public int $id;
    public string $guid;
    public string $countryGuid;
    public string $cityGuid;
    public string $zoneName;
    public int $status;
}
