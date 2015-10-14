<?php

namespace Ebess\ImageService;

use Ebess\ImageService\Filters\FitFilter;
use Illuminate\Support\ServiceProvider;

/**
 * Class ImageServiceProvider
 * @package Ebess\ImageService
 */
class ImageServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot()
    {
        if (! $this->app->routesAreCached()) {
            require __DIR__.'/routes.php';
        }

        $this->publishes([
            __DIR__ . '/../config/image-service.php' => config_path('image-service.php'),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/image-service.php', 'image-service'
        );

        $this->app->singleton('image.filters.fit', function() {
            return new FitFilter;
        });
    }
}