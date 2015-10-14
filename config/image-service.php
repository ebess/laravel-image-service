<?php

return [

    /**
     * storage options
     */
    'storage' => [
        'disk' => 'local',
        'root' => storage_path('/images'),
    ],

    /**
     * filter definitions
     */
    'filters' => [
        'default' => [
            'type' => 'fit',
            'options' => [
                'box' => [200, 200],
            ],
        ],
    ]
];