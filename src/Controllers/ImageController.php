<?php

namespace Ebess\ImageService\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Intervention\Image\ImageManager;

class ImageController
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var ImageManager
     */
    protected $imageManager;

    /**
     * @param Application $app
     * @param ImageManager $imageManager
     */
    public function __construct(Application $app, ImageManager $imageManager)
    {
        $this->app = $app;
        $this->imageManager = $imageManager;
    }

    /**
     * @param string $hash
     * @param string $filter
     * @return mixed
     */
    public function show($hash, $filter)
    {
        return $this->app->make('image.filter.' . $filter)
            ->process($this->imageManager->make($hash))
            ->response();
    }
}