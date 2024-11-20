<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\Middleware\AdminMiddleware;

class MiddlewareServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app['router']->aliasMiddleware('admin', AdminMiddleware::class);
    }

    public function boot(): void
    {
        //
    }
}