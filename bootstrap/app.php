<?php

use App\Http\Middleware\Activated;
use App\Http\Middleware\CorsMiddleware;
use App\Http\Middleware\DashboardLocalization;
use App\Http\Middleware\ForceJsonResponse;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\IsAcceptedFollowing;
use App\Http\Middleware\IsFollower;
use App\Http\Middleware\IsFollowing;
use App\Http\Middleware\MobileLocalization;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Middleware\RoleMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        // api: __DIR__.'/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function ($router) {
            Route::prefix('api/v1')
                ->middleware('api')
                ->name('api.v1.')
                ->group(base_path('routes/api.v1.php'));
            Route::prefix('api/v2')
                ->middleware('api')
                ->name('api.v2.')
                ->group(base_path('routes/api.v2.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(CorsMiddleware::class);
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        //
        $middleware->web(append: [
            SetLocale::class,
            HandleInertiaRequests::class,
        ]);
        $middleware->alias([
            'is.follower' => IsFollower::class,
            'is.following' => IsFollowing::class,
            'is.accepted.following' => IsAcceptedFollowing::class,
            'activated' => Activated::class,
            'role' => RoleMiddleware::class,
            'mobile.localization' => MobileLocalization::class,
            'json.response' => ForceJsonResponse::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
