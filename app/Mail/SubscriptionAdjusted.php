<?php

namespace App\Mail;

use App\Models\Member;
use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubscriptionAdjusted extends Mailable
{
    use Queueable, SerializesModels;

    public $member;
    public $subscription;
    public $oldAmount;
    public $newAmount;

    public function __construct(Member $member, Subscription $subscription, $oldAmount, $newAmount)
    {
        $this->member = $member;
        $this->subscription = $subscription;
        $this->oldAmount = $oldAmount;
        $this->newAmount = $newAmount;
    }

    public function build()
    {
        return $this->subject('Loan Payment Subscription Amount Updated')
                    ->view('emails.subscription-adjusted');
    }
}
