<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Providers\MiddlewareServiceProvider;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->appendToGroup('admin', \App\Http\Middleware\AdminMiddleware::class);
    })
    ->withProviders([
        MiddlewareServiceProvider::class,
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        // Register your exception handlers here
    })->create();
