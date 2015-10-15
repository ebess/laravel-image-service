<?php

namespace Ebess\ImageService\Controllers;

use Ebess\ImageService\Contracts\Filter;
use Illuminate\Contracts\Filesystem\Factory;
use Illuminate\Routing\Controller;
use Intervention\Image\ImageManager;

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
     * @param ImageManager $imageManager
     * @param Factory $filesystem
     */
    public function __construct(
        ImageManager $imageManager,
        Factory $filesystem
    ) {
        $this->imageManager = $imageManager;
        $this->filesystem = $filesystem;
    }

    /**
     * @param string $filterName
     * @param string $hash
     * @return mixed
     */
    public function show($filterName, $hash)
    {
        $filterData = config('image-service.filters.' . $filterName);
        $filterCheckHash = md5(json_encode(isset($filterData['options']) ? $filterData['options'] : []));

        $pathname = config('image-service.path') . '/' . $hash;
        $pathnameFilter = config('image-service.path') . '/' . $filterName . '/' . $filterCheckHash . '/' . $hash;
        $disk = $this->filesystem->disk(config('image-service.disk'));

        // if filter already used
        if ($disk->exists($pathnameFilter)) {
            return $this->imageManager->make($disk->get($pathnameFilter))
                ->response();
        }

        // apply filter on image
        $image = $this->imageManager->make($disk->get($pathname));

        /** @var Filter $filter */
        $filter = app('image.filters.' . $filterData['type']);

        // apply filter & cache the filtered image
        $image = $filter->process($image, isset($filterData['options']) ? $filterData['options'] : []);

        // cache if needed
        if (isset($filterData['cache']) && $filterData['cache']) {
            $disk->makeDirectory(config('image-service.path') . '/' . $filterName);
            $disk->put($pathnameFilter, $image->encode(null, 100));
        }

        return $image->response();
    }
}
