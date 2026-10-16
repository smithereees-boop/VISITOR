<?php
declare(strict_types=1);
session_start();
date_default_timezone_set('Africa/Kampala');
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$config = require __DIR__ . '/../config/database.php';
$db = new mysqli(
    $config['host'],
    $config['user'],
    $config['pass'],
    $config['name']
);
$db->set_charset('utf8mb4');
require __DIR__ . '/functions.php';