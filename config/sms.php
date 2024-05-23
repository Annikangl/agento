<?php

return [
    'driver' => env('SMS_DRIVER', 'mobizone'),

    'drivers' => [
        'mobizone' => [
            'url' => env('MOBIZONE_URL'),
            'api_key' => env('MOBIZONE_API_KEY')
        ]
    ]
];
