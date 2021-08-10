<?php

use App\Gateways\Factory;

return [

    Factory::ROTESSA => [
        'base_url' => env('ROTESSA_BASE_URL', 'https://api.rotessa.com/v1'),
        'api_key' => env('ROTESSA_API_KEY')
    ],

    Factory::CARDKNOX => [
        'subscription_base_url' => env('CARDKNOX_RECURRING_API_BASE_URL', 'https://api.cardknox.com/v2'),
        'subscription_api_version' => env('CARDKNOX_RECURRING_API_VERSION', '2.0'),
        'transaction_base_url' => env('CARDKNOX_TRANSACTION_API_BASE_URL', 'https://x1.cardknox.com/gatewayjson'),
        'transaction_api_version' => env('CARDKNOX_TRANSACTION_API_VERSION', '4.5.9'),
        'api_key' => env('CARDKNOX_API_KEY'),
    ],

    Factory::MANUAL => [

    ],

];
