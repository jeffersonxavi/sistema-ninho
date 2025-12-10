<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    // ... código existente ...

    /**
     * Define the routes for the application.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // ----------------------------------------------------------------------------------
        // >> CORREÇÃO: REGISTRO DO MIDDLEWARE PERSONALIZADO AQUI
        // ----------------------------------------------------------------------------------
        Route::middleware('web')
            ->alias('admin', \App\Http\Middleware\IsAdmin::class);
        // ----------------------------------------------------------------------------------

        $this->routes(function () {
            // ... código existente para api.php e web.php ...
        });
    }
}