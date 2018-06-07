<?php

namespace Cloudpaths;

use Illuminate\Config\Repository;
use Illuminate\Support\ServiceProvider;

class CloudpathsServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/cloudpaths.php' => config_path('cloudpaths.php'),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Bind the Factory implementation.
        $this->app->bind(
            Contracts\Factory::class,
            DirFactory::class
        );

        $this->app->singleton('Cloudpaths', function ($app) {
            return new Cloudpaths(
                $this->app,
                new Repository($app['config']['cloudpaths'])
            );
        });
    }
}
