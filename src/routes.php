<?php

app('router')->get('/images/{filterName}/{hash}/{name}', [
    'as' => 'image-service.show',
    'uses' => Ebess\ImageService\Controllers\ImageController::class . '@show'
]);
