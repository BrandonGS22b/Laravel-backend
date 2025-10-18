<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider; 
use App\Repositories\ContribuyenteRepository;
use App\Repositories\Interfaces\ContribuyenteRepositoryInterface;


use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\UserRepository;



class AppServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        $this->app->bind(
            ContribuyenteRepositoryInterface::class,
            ContribuyenteRepository::class,
        );

        //registramos para que sepa cuando usar la clase en el controlador de usuarios
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class

        );
    }

    
    public function boot(): void
    {
        //
    }
}
