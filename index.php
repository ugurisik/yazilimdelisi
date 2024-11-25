<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    require_once 'init.php';
    $system = new System();
    $system->run();
} catch (Exception $e) {
    echo $e->getMessage();
}
