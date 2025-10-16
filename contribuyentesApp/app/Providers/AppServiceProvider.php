<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider; // ✅ CORRECTO
use App\Repositories\ContribuyenteRepository;
use App\Repositories\Interfaces\ContribuyenteRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            ContribuyenteRepositoryInterface::class,
            ContribuyenteRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
