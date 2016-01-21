<?php

return [

    /**
     * filesystem disk to use
     */
    'disk' => 'local',

    /**
     * root path for storing the images
     */
    'path' => 'images',

    /**
     * filter definitions
     */
    'filters' => [

        'original' => [
            'type' => 'original',
            'cache' => false,
        ],

        'thumbnail' => [
            'type' => 'fit',
            'cache' => true,
            'options' => [
                'box' => [200, 250],
            ],
        ],

        'gray-scale' => [
            'type' => 'gray-scale',
            'cache' => true,
        ],

        'old-image' => [
            'type' => 'combine',
            'cache' => true,
            'options' => [
                'thumbnail', 'gray-scale'
            ]
        ],

    ]
];
