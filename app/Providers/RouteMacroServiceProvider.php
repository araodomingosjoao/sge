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
        Route::macro('apiCrud', function ($name, $controller, $globalMiddleware = [], $specificMiddlewares = []) {
            Route::middleware(!empty($globalMiddleware) ? $globalMiddleware : [])->prefix($name)->group(function () use ($controller, $specificMiddlewares) {
                Route::post('/', [$controller, 'create'])->middleware($specificMiddlewares['create'] ?? []);
                Route::get('/', [$controller, 'index'])->middleware($specificMiddlewares['index'] ?? []);
                Route::get('/{id}', [$controller, 'read'])->middleware($specificMiddlewares['read'] ?? []);
                Route::put('/{id}', [$controller, 'update'])->middleware($specificMiddlewares['update'] ?? []);
                Route::delete('/{id}', [$controller, 'delete'])->middleware($specificMiddlewares['delete'] ?? []);
            });
        });
    }
}
