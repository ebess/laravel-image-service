<?php

if (! function_exists('images')) {
    /**
     * Return a image service handler.
     *
     * @return \Ebess\ImageService\Contracts\Handler
     */
    function images()
    {
        return app('Ebess\ImageService\Contracts\Handler');
    }
}

if (! function_exists('image')) {
    /**
     * @param string $hash
     * @param string|null $filterName
     * @return string
     */
    function image($hash, $filterName = null)
    {
        return images()->url($hash, $filterName);
    }
}
