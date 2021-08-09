<?php


namespace App\Gateways\Rotessa;


use App\Models\Subscription;

class Frequencies
{
    public static array $toRotessaFrequencies = [
        Subscription::FREQUENCY_ONCE => 'Once',
        Subscription::FREQUENCY_WEEKLY => 'Weekly',
//        Subscription::FREQUENCY_BIWEEKLY => 'Every Other Week',
        Subscription::FREQUENCY_MONTHLY => 'Monthly',
//        Subscription::FREQUENCY_BIMONTHLY => 'Every Other Month',
//        Subscription::FREQUENCY_QUARTERLY => 'Quarterly',
//        Subscription::FREQUENCY_SEMI_ANNUALLY => 'Semi-Annually',
        Subscription::FREQUENCY_YEARLY => 'Yearly',
    ];

    public static array $fromRotessaFrequencies = [
        'Once'              => Subscription::FREQUENCY_ONCE,
        'Weekly'            => Subscription::FREQUENCY_WEEKLY,
//        'Every Other Week'  => Subscription::FREQUENCY_BIWEEKLY,
        'Monthly'           => Subscription::FREQUENCY_MONTHLY,
//        'Every Other Month' => Subscription::FREQUENCY_BIMONTHLY,
//        'Quarterly'         => Subscription::FREQUENCY_QUARTERLY,
//        'Semi-Annually'     => Subscription::FREQUENCY_SEMI_ANNUALLY,
        'Yearly'            => Subscription::FREQUENCY_YEARLY,
    ];
}
