<?php

namespace Ebess\ImageService\Handlers;

use Ebess\ImageService\Contracts\Handler;
use Ebess\ImageService\Contracts\ImageDeliverable;
use Illuminate\Contracts\Filesystem\Filesystem;
use Intervention\Image\ImageManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageHandler implements Handler
{
    /**
     * @var ImageManager
     */
    protected $imageManager;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * @param ImageManager $imageManager
     * @param Filesystem $filesystem
     */
    public function __construct(
        ImageManager $imageManager,
        Filesystem $filesystem
    ) {
        $this->imageManager = $imageManager;
        $this->filesystem = $filesystem;
    }

    /**
     * uploads an image with given filename and options
     *
     * @param mixed $image
     * @param string $filename
     * @param array $options
     * @return mixed
     */
    public function upload($image, $filename = null, array $options = [])
    {
        // if uploaded image, leave client name by default
        if ($image instanceof UploadedFile && $filename === null) {
            $filename = $image->getClientOriginalName();
        }

        $hash = str_random();
        $this->filesystem->put(
            implode('/', [config('image-service.path'), 'original', $hash, $filename]),
            $this->imageManager->make($image)->encode(null, 100)
        );

        // if eager creating filtered images is given
        if (isset($options['eager'])) {
            $eager = $options['eager'];

            // if create for all filters
            if ($eager === '*') {
                $filters = array_keys(config('image-service.filters'));
                $filters = array_diff($filters, ['original']);
            } else {
                $filters = $eager;
            }

            $this->create($hash, $filename, $filters);
        }

        return $hash . '/' . $filename;
    }

    /**
     * checks if hash can be processed and returns it in case it is
     *
     * @param string|ImageDeliverable $hash
     * @return string
     */
    private function isHashValid($hash)
    {
        if ($hash instanceof ImageDeliverable) {
            $hash = $hash->getImageServiceHash();
        }

        // if invalid hash
        if (!is_string($hash) && !($hash instanceof ImageDeliverable)) {
            throw new \InvalidArgumentException('The hash for image service has to be either a string or a '.ImageDeliverable::class.' instance - '.gettype($hash).' given.');
        }

        return $hash;
    }

    /**
     * deletes all saved image data for given hash
     *
     * @param string $hash
     * @return mixed
     */
    public function remove($hash)
    {
        $hash = $this->isHashValid($hash);

        foreach (array_keys(config('image-service.filters')) as $filterName) {
            $path = implode('/', [config('image-service.path'), $filterName, $hash]);
            if ($this->filesystem->exists($path)) {
                $this->filesystem->delete($path);
            }
        }
    }

    /**
     * delivery the proper url for image with given hash and filter name
     *
     * @param string|object $hash
     * @param string|null $filterName
     * @return string
     */
    public function url($hash, $filterName = null)
    {
        $hash = $this->isHashValid($hash);

        // if hash is empty
        if (empty($hash)) {
            throw new \InvalidArgumentException('The given hash is empty.');
        }

        // if no filter name given, take the default one
        if ($filterName === null) {
            $filterName = config('image-service.default-filter');
        }

        return route('image-service.show', compact('hash', 'filterName'));
    }

    /**
     * create filtered image
     *
     * @param string $hash
     * @param string $filename
     * @param string[]|string $filters
     * @return void
     */
    public function create($hash, $filename, $filters)
    {
        $filteredImages = [];
        $imageContent = $this->filesystem->get(
            config('image-service.path') . '/original/' . $hash . '/' . $filename
        );
        $image = $this->imageManager->make($imageContent);

        // if just one filter as string
        if (is_string($filters)) {
            $filters = [$filters];
        }

        foreach ($filters as $filterName) {
            $filterData = config('image-service.filters.' . $filterName);
            /** @var Filter $filter */
            $filter = app('image.filters.' . $filterData['type']);
            $filteredImage = $filter->process($image, isset($filterData['options']) ? $filterData['options'] : []);

            // cache if needed
            if (isset($filterData['cache']) && $filterData['cache'] === true) {
                $filterPath = config('image-service.path') . '/' . $filterName . '/' . $hash;
                $this->filesystem->makeDirectory($filterPath);
                $this->filesystem->put($filterPath . '/' . $filename, $filteredImage->encode(null, 100));
            }

            // save to return it
            $filteredImages[$filterName] = $filteredImage;
        }

        return $filteredImages;
    }
}
