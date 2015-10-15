<?php

namespace Ebess\ImageService\Filters;

use Ebess\ImageService\Contracts\Filter;
use Intervention\Image\Image;

class OriginalFilter implements Filter
{
    /**
     * @param Image $image
     * @param array $options
     * @return Image
     */
    public function process(Image $image, array $options)
    {
        return $image;
    }
}
