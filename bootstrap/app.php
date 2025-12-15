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
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            
            'CheckAuth'=>'\App\Http\Middleware\CheckAuth::class',
            'CheckLogin'=> '\App\Http\Middleware\CheckLogin::class',
            'CheckAdmin'=> '\App\Http\Middleware\CheckAdmin::class',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
