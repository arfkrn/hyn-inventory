<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Bahan;
use App\Observers\BahanObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Bahan::observe(BahanObserver::class);
    }
}
