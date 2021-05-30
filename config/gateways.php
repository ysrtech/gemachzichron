<?php

return [

    'manual' => [
        'name' => 'Manual',
    ],

    'rotessa' => [
        'name' => 'Rotessa',
        'base_url' => env('ROTESSA_BASE_URL', 'https://api.rotessa.com/v1'),
        'api_key' => env('ROTESSA_API_KEY')
    ],

];
