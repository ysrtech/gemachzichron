<?php

use App\Gateways\Factory;

return [

    Factory::ROTESSA => [
        'base_url' => env('ROTESSA_BASE_URL', 'https://api.rotessa.com/v1'),
        'api_key' => env('ROTESSA_API_KEY')
    ],

    Factory::CARDKNOX => [

    ],

    Factory::MANUAL => [

    ],

];
