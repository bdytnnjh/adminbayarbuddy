<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\FirestoreService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Ensure every type-hint for FirestoreService resolves to the same wrapper
        $this->app->singleton(FirestoreService::class, function () {
            return new FirestoreService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
