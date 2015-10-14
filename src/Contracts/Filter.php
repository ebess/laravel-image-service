<?php

namespace Ebess\ImageService\Contracts;

use Intervention\Image\Image;

interface Filter
{
    /**
     * @param Image $image
     * @param array $options
     * @return Image
     */
    public function process(Image $image, array $options);
}