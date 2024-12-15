<?php

namespace App\models;
use Core\Model;

class MapCountry extends Model
{
    public string $tableName = 'country';
    public int $id;
    public string $guid = '';
    public string $commonName;
    public string $nativeName;
    public string $iso2;
    public string $iso3;
    public string $currency;
    public string $phoneCode;
    public string $capital;
    public string $region;
    public string $subRegion;
    public string $languages;
    public string $latLng;
    public int $status;
}
