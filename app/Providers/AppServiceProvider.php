<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //  Date::use(CarbonImmutable::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Inertia::share([
            'host' => function () {
                return env('APP_URL');
            },
            'locale' => function () {
                return app()->getLocale();
            },
            'language' => function () {
                if (!file_exists(resource_path('lang/' . app()->getLocale()
                    . '/' . app()->getLocale() . '.json'))) {
                    return [];
                }
                return json_decode(
                    file_get_contents(
                        resource_path('lang/' . app()->getLocale() . '/'
                            . app()->getLocale() . '.json')
                    ),
                    true
                );
            },
        ]);
    }
}
