<?php

namespace Cloudpaths;

use Illuminate\Config\Repository;
use Illuminate\Support\ServiceProvider;

class CloudpathsServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

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
        $this->app->singleton('Cloudpaths', function ($app) {
            return new Cloudpaths(
                new Repository($this->app, $app['config']['cloudpaths'])
            );
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['Cloudpaths'];
    }
}
