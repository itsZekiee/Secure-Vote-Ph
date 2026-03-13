<?php

use Illuminate\Support\Facades\File;

/*
|--------------------------------------------------------------------------
| Vercel Storage Initialization
|--------------------------------------------------------------------------
|
| Laravel requires a writeable storage directory for various features.
| In Vercel's serverless environment, only /tmp is writeable.
|
*/

if (env('VERCEL')) {
    $paths = [
        '/tmp/storage/app/public',
        '/tmp/storage/framework/cache',
        '/tmp/storage/framework/sessions',
        '/tmp/storage/framework/views',
        '/tmp/storage/logs',
    ];

    foreach ($paths as $path) {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }
}

require __DIR__ . '/../public/index.php';
