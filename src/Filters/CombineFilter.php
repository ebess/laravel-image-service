<?php

namespace Ebess\ImageService\Filters;

use Ebess\ImageService\Contracts\Filter;
use Intervention\Image\Image;

class CombineFilter implements Filter
{
    /**
     * @param Image $image
     * @param array $options
     * @return Image
     */
    public function process(Image $image, array $options)
    {
        // loop through all the filters and process them
        foreach ($options as $filterName) {
            $filterData = config('image-service.filters.' . $filterName);
            $filter = app('image.filters.' . $filterData['type']);
            $image = $filter->process($image, isset($filterData['options']) ? $filterData['options'] : []);
        }

        return $image;
    }
}
