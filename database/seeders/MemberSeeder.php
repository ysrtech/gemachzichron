<?php

namespace Database\Seeders;

use App\Models\Loan;
use App\Models\Member;
use App\Models\Membership;
use Illuminate\Database\Seeder;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Member::factory()
            ->times(500)
            ->hasDependents(3, fn (array $attributes, Member $member) => ['last_name' => $member->last_name])
            ->has(
                Membership::factory(1)
                    ->hasSubscriptions(4)
                    ->has(Loan::factory()
                            ->count(2)
                            ->state(function (array $attributes, Membership $membership) {
                                return ['dependent_id' => $membership->member->dependents->random()->id];
                            })
                    )
            )
            ->create();

        $members = Member::inRandomOrder()->limit(400)->get();

        Loan::each(fn($loan) => $loan->endorsements()->sync($members->random(4)));
    }
}
