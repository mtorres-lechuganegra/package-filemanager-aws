<?php

namespace LechugaNegra\AwsFileManager\Providers;

use Illuminate\Support\Facades\Route;
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
        // Registrar archivo de configuración principal
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/filemanageraws.php',
            'filemanager_aws'
        );
    }

    /**
     * Realizar las configuraciones necesarias.
     *
     * @return void
     */
    public function boot()
    {
        // Cargar configuración predeterminada desde el paquete
        $this->publishes([
            __DIR__ . '/../../config/filemanageraws.php' => config_path('filemanageraws.php'),
        ], 'filemanageraws-config');
        
        // Cargar rutas dinámicas según la versión especificada en config
        $this->loadVersionedRoutes();
    }

    /**
     * Carga el archivo de rutas correspondiente a la versión configurada.
     *
     * @return void
     */
    protected function loadVersionedRoutes()
    {
        $version = config('filemanageraws.version', 'v1');
        $routesPath = __DIR__ . "/../Routes/{$version}/api.php";

        if (file_exists($routesPath)) {
            Route::prefix("api/{$version}")
                ->group($routesPath);
        } else {
            throw new \Exception("Routes file not found for version '{$version}'.");
        }
    }
}
