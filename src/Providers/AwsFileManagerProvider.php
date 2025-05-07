<?php

namespace LechugaNegra\AwsFileManager\Providers;

use Illuminate\Support\ServiceProvider;

class AwsFileManagerProvider extends ServiceProvider
{
    /**
     * Realizar el registro de servicios.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Realizar las configuraciones necesarias.
     *
     * @return void
     */
    public function boot()
    {
        // Cargar rutas de api.php
        $this->loadRoutesFrom(__DIR__.'/../Routes/api.php');
    }
}
