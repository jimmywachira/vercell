<?php

define('LARAVEL_START', microtime(true));

// Polyfill for ReflectionProperty::isVirtual() (PHP 8.1+)
if (!method_exists('ReflectionProperty', 'isVirtual')) {
    if (!class_exists('ReflectionPropertyPolyfill')) {
        class ReflectionPropertyPolyfill extends ReflectionProperty {
            public function isVirtual() {
                return false;
            }
        }
    }
}

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
} catch (Throwable $e) {
    error_log('Laravel Error: ' . $e->getMessage());
    error_log('Error File: ' . $e->getFile() . ':' . $e->getLine());
    error_log('Stack Trace: ' . $e->getTraceAsString());

        'file' => $e->getFile(),
        'line' => $e->getLine(),
    ]);
    exit(1);
}
