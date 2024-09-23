<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RouteMacroServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Route::macro('apiCrud', function ($name, $controller) {
            Route::prefix($name)->group(function () use ($controller) {
                Route::post('/', [$controller, 'create']);
                Route::get('/{id}', [$controller, 'read']);
                Route::put('/{id}', [$controller, 'update']);
                Route::delete('/{id}', [$controller, 'delete']);
                Route::get('/', [$controller, 'index']);
            });
        });
    }
}
