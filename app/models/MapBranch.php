<?php

use Core\Model;

class MapBranch extends Model
{
    public string $tableName = 'branch';
    public int $id;
    public string $guid;
    public string $branchName;
    public string $countryGuid; 
    public string $cityGuid;
    public string $zoneGuid;
    public string $taxOfficeGuid;
    public string $taxNumber;
    public string $phone1;
    public string $phone2;
    public string $email;
    public string $address;
    public string $mapLocationData;
    public int $status;
    public DateTime $createdDate;
    public DateTime $updatedDate;
    public string $notes;
}
