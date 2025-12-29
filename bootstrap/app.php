<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\AppLocked;
use App\Http\Middleware\EmergencyModeMiddleware;



return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channel.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
         $middleware->alias([
            'admin' => AdminMiddleware::class,
            'applock'  =>  AppLocked::class,
            'emergency' => EmergencyModeMiddleware::class,

        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
