<?php

define('LARAVEL_START', microtime(true));

try {
    // Use /tmp for storage in serverless environments
    if (!is_dir('/tmp/storage')) {
        mkdir('/tmp/storage', 0755, true);
        mkdir('/tmp/storage/framework', 0755, true);
        mkdir('/tmp/storage/framework/sessions', 0755, true);
        mkdir('/tmp/storage/framework/cache', 0755, true);
        mkdir('/tmp/storage/framework/views', 0755, true);
        mkdir('/tmp/storage/logs', 0755, true);
    }

    $maintenance = __DIR__ . '/../storage/framework/maintenance.php';
    if (file_exists($maintenance)) {
        require $maintenance;
    }

    require __DIR__ . '/../vendor/autoload.php';

    /** @var \Illuminate\Foundation\Application $app */
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    // Override storage path for serverless
    $app->useStoragePath('/tmp/storage');

    // Ensure debug mode is off in production
    if (getenv('APP_ENV') === 'production') {
        putenv('APP_DEBUG=false');
    }

    $app->handleRequest(\Illuminate\Http\Request::capture());
} catch (Error $e) {
    // Check if this is the ReflectionProperty::isVirtual() error from var-dumper
    if (strpos($e->getMessage(), 'isVirtual') !== false && strpos($e->getFile(), 'Caster.php') !== false) {
        // Compatibility error, return a simple homepage redirect
        header('Location: /', true, 302);
        exit(0);
    }

    error_log('Laravel Error: ' . $e->getMessage());
    error_log('Error File: ' . $e->getFile() . ':' . $e->getLine());

    header('Content-Type: application/json', true, 500);
    echo json_encode(['error' => 'Internal Server Error']);
    exit(1);
} catch (Throwable $e) {
    error_log('Laravel Error: ' . $e->getMessage());
    error_log('Error File: ' . $e->getFile() . ':' . $e->getLine());

    header('Content-Type: application/json', true, 500);
    echo json_encode(['error' => 'Internal Server Error']);
    exit(1);
}
