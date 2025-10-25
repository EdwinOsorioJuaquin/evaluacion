<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Graduate;
use App\Observers\GraduateObserver;

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
        // Registrar el observer para asignación automática de encuestas
        Graduate::observe(GraduateObserver::class);

        
    }
}
