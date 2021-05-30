<?php

namespace App\Listeners;

use App\Events\MemberUpdated;
use App\Exceptions\NotImplementedException;
use App\Facades\Gateway;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class MemberUpdateNotifyGateways implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  MemberUpdated  $event
     * @return void
     */
    public function handle(MemberUpdated $event)
    {
        $event->member
            ->load('membership.paymentMethods')
            ->membership
            ->paymentMethods
            ->each(function ($paymentMethod) {
                try {
                    Gateway::initialize($paymentMethod->gateway)
                        ->updateCustomer($paymentMethod, []);
                } catch (NotImplementedException $exception) {}
            });
    }
}
