<?php

namespace Rudi97277\ExportDb;

use Illuminate\Support\ServiceProvider;

class ExportServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Load migrations if you have any in the package
        $this->loadMigrationsFrom(__DIR__ . '/Migrations');
    }

    public function register()
    {
        // If your package needs to register anything in the container
    }
}
