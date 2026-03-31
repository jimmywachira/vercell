<?php

define('LARAVEL_START', microtime(true));

$maintenance = __DIR__ . '/../storage/framework/maintenance.php';
if (file_exists($maintenance)) {
    require $maintenance;
}

require __DIR__ . '/../vendor/autoload.php';

/** @var \Illuminate\Foundation\Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->handleRequest(\Illuminate\Http\Request::capture());
