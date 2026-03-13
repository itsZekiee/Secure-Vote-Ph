<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register middleware aliases
        $middleware->alias([
            'voter.auth' => \App\Http\Middleware\VoterAuth::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'subadmin' => \App\Http\Middleware\SubAdminMiddleware::class,
            'ip.control' => \App\Http\Middleware\IpAccessControlMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->usingStoragePath(env('VERCEL') ? '/tmp/storage' : null)
    ->create();
