<?php

namespace Ebess\ImageService;

use Ebess\ImageService\Console\TruncateCommand;
use Ebess\ImageService\Filters\CombineFilter;
use Ebess\ImageService\Filters\FitFilter;
use Ebess\ImageService\Filters\GrayScaleFilter;
use Ebess\ImageService\Filters\OriginalFilter;
use Ebess\ImageService\Handlers\ImageHandler;
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
            require __DIR__ . '/routes.php';
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
            __DIR__ . '/../config/image-service.php', 'image-service'
        );

        $this->app->bind('image-service.filesystem', function() {
            return $this->app->make('Illuminate\Contracts\Filesystem\Factory')->disk('local');
        });

        $this->app->singleton('Ebess\ImageService\Contracts\Handler', function() {
            return new ImageHandler(
                $this->app->make('Intervention\Image\ImageManager'),
                $this->app->make('image-service.filesystem')
            );
        });

        $this->registerFilters();
        $this->registerCommands();
    }

    /**
     * register default filters for image service
     */
    public function registerFilters()
    {
        $this->app->singleton('image.filters.original', function () {
            return new OriginalFilter;
        });

        $this->app->singleton('image.filters.fit', function () {
            return new FitFilter;
        });

        $this->app->singleton('image.filters.gray-scale', function () {
            return new GrayScaleFilter;
        });

        $this->app->singleton('image.filters.combine', function () {
            return new CombineFilter;
        });
    }

    /**
     * register all needed commands for image service
     */
    public function registerCommands()
    {
        $this->app->bind('command.image-service.truncate', function () {
            return new TruncateCommand($this->app->make('image-service.filesystem'));
        });

        $this->commands(['command.image-service.truncate']);
    }
}
