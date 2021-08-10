<?php


namespace App\Gateways\Cardknox;


use App\Models\Subscription;

class Frequencies
{
    public static array $toCardknoxFrequencies = [
        Subscription::FREQUENCY_ONCE => 'day', // using day as it is not used as a frequency
        Subscription::FREQUENCY_WEEKLY => 'week',
        Subscription::FREQUENCY_MONTHLY => 'month',
        Subscription::FREQUENCY_YEARLY => 'year',
    ];

    public static array $fromCardknoxFrequencies = [
        'day'             => Subscription::FREQUENCY_ONCE, // using day as it is not used as a frequency
        'week'            => Subscription::FREQUENCY_WEEKLY,
        'month'           => Subscription::FREQUENCY_MONTHLY,
        'year'            => Subscription::FREQUENCY_YEARLY,
    ];
}
