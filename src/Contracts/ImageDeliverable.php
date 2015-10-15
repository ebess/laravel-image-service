<?php

namespace Ebess\ImageService\Contracts;

interface ImageDeliverable
{
    /**
     * return the image service hash
     *
     * @return string
     */
    public function getImageServiceHash();
}
