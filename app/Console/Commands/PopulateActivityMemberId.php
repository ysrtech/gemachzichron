<?php

namespace App\Console\Commands;

use App\Models\Loan;
use App\Models\Member;
use App\Models\Subscription;
use App\Models\Transaction;
use Illuminate\Console\Command;
use Spatie\Activitylog\Models\Activity;

class PopulateActivityMemberId extends Command
{
    protected $signature = 'activity:populate-member-id';
    protected $description = 'Populate member_id field for existing activities';

    public function handle()
    {
        $this->info('Populating member_id for existing activities...');

        $updated = 0;
        
        Activity::whereNull('member_id')->chunk(100, function ($activities) use (&$updated) {
            foreach ($activities as $activity) {
                $memberId = null;

                // Member activities - use the subject_id directly
                if ($activity->subject_type === Member::class && $activity->subject_id) {
                    $memberId = $activity->subject_id;
                }
                // Loan activities
                elseif (in_array($activity->subject_type, [Loan::class, 'App\\Models\\Loan'])) {
                    // Try to find the loan first
                    if ($activity->subject_id && $subject = Loan::find($activity->subject_id)) {
                        $memberId = $subject->member_id;
                    }
                    // If loan is deleted, check properties
                    elseif (isset($activity->properties['attributes']['member_id'])) {
                        $memberId = $activity->properties['attributes']['member_id'];
                    }
                }
                // Subscription activities
                elseif (in_array($activity->subject_type, [Subscription::class, 'App\\Models\\Subscription'])) {
                    if ($activity->subject_id && $subject = Subscription::find($activity->subject_id)) {
                        $memberId = $subject->member_id;
                    }
                    elseif (isset($activity->properties['attributes']['member_id'])) {
                        $memberId = $activity->properties['attributes']['member_id'];
                    }
                }
                // Transaction activities
                elseif (in_array($activity->subject_type, [Transaction::class, 'App\\Models\\Transaction'])) {
                    if ($activity->subject_id && $subject = Transaction::find($activity->subject_id)) {
                        $memberId = $subject->member_id;
                    }
                    elseif (isset($activity->properties['attributes']['member_id'])) {
                        $memberId = $activity->properties['attributes']['member_id'];
                    }
                }

                if ($memberId) {
                    $activity->update(['member_id' => $memberId]);
                    $updated++;
                }
            }
        });

        $this->info("Done! Updated {$updated} activities.");
        
        return 0;
    }
}
