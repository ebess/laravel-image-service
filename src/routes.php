<?php

app('router')->get('/images/{filterName}/{hash}', [
    'as' => 'image-service.show',
    'uses' => Ebess\ImageService\Controllers\ImageController::class . '@show'
]);
