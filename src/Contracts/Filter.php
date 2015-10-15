<?php

namespace Ebess\ImageService\Contracts;

use Intervention\Image\Image;

interface Filter
{
    /**
     * process the instance of image and return it again
     *
     * @param Image $image
     * @param array $options
     * @return Image
     */
    public function process(Image $image, array $options);
}
