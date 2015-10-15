<?php

namespace Ebess\ImageService\Handlers;

use Ebess\ImageService\Contracts\Handler;
use Ebess\ImageService\Contracts\ImageDeliverable;
use Illuminate\Contracts\Filesystem\Filesystem;
use Intervention\Image\ImageManager;

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
    public function upload($image, $filename, array $options = [])
    {
        $hash = str_random() . '-' . $filename;
        $this->filesystem->put(
            config('image-service.path') . '/' . $hash,
            $this->imageManager->make($image)->encode(null, 100)
        );

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
        $this->filesystem->delete(config('image-service.path') . '/' . $hash);
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
        // if hash is a image service deliverable object, get the hash through the interface
        if ($hash instanceof ImageDeliverable) {
            $hash = $hash->getImageServiceHash();
        }

        // if invalid hash
        if (!is_string($hash)) {
            throw new \InvalidArgumentException('The hash for image service has to be either a string or a Ebess\ImageService\Contracts\ImageDeliverable instance.');
        }

        // if no filter name given, take the default one
        if ($filterName === null) {
            $filterName = config('image-service.default-filter');
        }

        return route('image-service.show', compact('hash', 'filterName'));
    }
}
