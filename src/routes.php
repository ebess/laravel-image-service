<?php

app('router')->get('/images/{hash}/{filter}/{name?}', [
    'as' => 'image.show',
    'uses' => Ebess\ImageService\Controllers\ImageController::class . '@show'
]);