<?php

namespace Ebess\ImageService\Controllers;

use Illuminate\Contracts\Filesystem\Factory;
use Illuminate\Routing\Controller;
use Intervention\Image\ImageManager;
use Ebess\ImageService\Contracts\Handler as ImageServiceHandler;

class ImageController extends Controller
{
    /**
     * @var ImageManager
     */
    protected $imageManager;

    /**
     * @var Factory
     */
    protected $filesystem;

    /**
     * @var ImageServiceHandler
     */
    protected $imageService;

    /**
     * @param ImageManager $imageManager
     * @param Factory $filesystem
     * @param ImageServiceHandler $imageService
     */
    public function __construct(
        ImageManager $imageManager,
        Factory $filesystem,
        ImageServiceHandler $imageService
    ) {
        $this->imageManager = $imageManager;
        $this->filesystem = $filesystem;
        $this->imageService = $imageService;
    }

    /**
     * @param string $filterName
     * @param string $hash
     * @param string $name
     * @return mixed
     */
    public function show($filterName, $hash, $name)
    {
        // filter image
        $filteredImages = $this->imageService->create($hash, $name, $filterName);

        return $filteredImages[$filterName]->response();
    }
}
