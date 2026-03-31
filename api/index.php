<?php

define('LARAVEL_START', microtime(true));

try {
    $tmpCacheDir = '/tmp/storage/framework/cache';

    // Force production-safe flags before Laravel boots.
    if (getenv('APP_ENV') === 'production') {
        putenv('APP_DEBUG=false');
        $_ENV['APP_DEBUG'] = 'false';
        $_SERVER['APP_DEBUG'] = 'false';
    }

    // Route all Laravel cache artifacts to writable storage on serverless.
    putenv('APP_SERVICES_CACHE=' . $tmpCacheDir . '/services.php');
    putenv('APP_PACKAGES_CACHE=' . $tmpCacheDir . '/packages.php');
    putenv('APP_CONFIG_CACHE=' . $tmpCacheDir . '/config.php');
    putenv('APP_ROUTES_CACHE=' . $tmpCacheDir . '/routes-v7.php');
    putenv('APP_EVENTS_CACHE=' . $tmpCacheDir . '/events.php');

    $_ENV['APP_SERVICES_CACHE'] = $tmpCacheDir . '/services.php';
    $_ENV['APP_PACKAGES_CACHE'] = $tmpCacheDir . '/packages.php';
    $_ENV['APP_CONFIG_CACHE'] = $tmpCacheDir . '/config.php';
    $_ENV['APP_ROUTES_CACHE'] = $tmpCacheDir . '/routes-v7.php';
    $_ENV['APP_EVENTS_CACHE'] = $tmpCacheDir . '/events.php';

    $_SERVER['APP_SERVICES_CACHE'] = $tmpCacheDir . '/services.php';
    $_SERVER['APP_PACKAGES_CACHE'] = $tmpCacheDir . '/packages.php';
    $_SERVER['APP_CONFIG_CACHE'] = $tmpCacheDir . '/config.php';
    $_SERVER['APP_ROUTES_CACHE'] = $tmpCacheDir . '/routes-v7.php';
    $_SERVER['APP_EVENTS_CACHE'] = $tmpCacheDir . '/events.php';

    // Use /tmp for storage in serverless environments
    if (!is_dir('/tmp/storage/framework/sessions')) {
        mkdir('/tmp/storage/framework/sessions', 0755, true);
    }
    if (!is_dir('/tmp/storage/framework/cache')) {
        mkdir('/tmp/storage/framework/cache', 0755, true);
    }
    if (!is_dir('/tmp/storage/framework/views')) {
        mkdir('/tmp/storage/framework/views', 0755, true);
    }
    if (!is_dir('/tmp/storage/logs')) {
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

    $app->handleRequest(\Illuminate\Http\Request::capture());
} catch (Error $e) {
    // Check if this is the ReflectionProperty::isVirtual() error from var-dumper
    if (strpos($e->getMessage(), 'isVirtual') !== false && strpos($e->getFile(), 'Caster.php') !== false) {
        error_log('Compatibility Error: ' . $e->getMessage());
        error_log('Compatibility File: ' . $e->getFile() . ':' . $e->getLine());

        header('Content-Type: application/json', true, 500);
        echo json_encode([
            'error' => 'Internal Server Error',
            'message' => 'PHP runtime is incompatible with current vendor packages.',
        ]);
        exit(1);
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
