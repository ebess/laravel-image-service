<?php

namespace Ebess\ImageService\Contracts;

interface Handler
{
    /**
     * uploads an image with given filename and options
     *
     * @param mixed $image
     * @param string $filename
     * @param array $options
     * @return mixed
     */
    public function upload($image, $filename, array $options = []);

    /**
     * deletes all saved image data for given hash
     *
     * @param string $hash
     * @return mixed
     */
    public function remove($hash);

    /**
     * delivery the proper url for image with given hash and filter name
     *
     * @param string|object $hash
     * @param string|null $filterName
     * @return string
     */
    public function url($hash, $filterName = null);
}
