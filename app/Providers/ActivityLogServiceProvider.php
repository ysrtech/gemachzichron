<?php

namespace App\Providers;

use App\Models\Loan;
use App\Models\Member;
use App\Models\Subscription;
use App\Models\Transaction;
use Illuminate\Support\ServiceProvider;
use Spatie\Activitylog\Models\Activity;

class ActivityLogServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Activity::saving(function (Activity $activity) {
            // Skip if member_id is already set
            if ($activity->member_id) {
                return;
            }

            $memberId = null;

            // Determine member_id based on the subject type
            if ($activity->subject_type === Member::class && $activity->subject) {
                $memberId = $activity->subject->id;
            } elseif ($activity->subject_type === Loan::class && $activity->subject) {
                $memberId = $activity->subject->member_id;
            } elseif ($activity->subject_type === Subscription::class && $activity->subject) {
                $memberId = $activity->subject->member_id;
            } elseif ($activity->subject_type === Transaction::class && $activity->subject) {
                $memberId = $activity->subject->member_id;
            }

            $activity->member_id = $memberId;
        });
    }
}
