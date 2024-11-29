<?php
require_once __DIR__ . '/core/autoload.php';

use App\helpers\utils\CronManager;

$cronManager = CronManager::getInstance();


$cronManager->addJob('her_5_dakika', '*/5 * * * *', function() {
    // İşlemler
});

$cronManager->run();
