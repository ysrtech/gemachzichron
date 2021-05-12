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
            ->hasDependents(3)
            ->has(Membership::factory(1)
                ->has(Loan::factory()
                    ->count(2)
                    ->state(fn(array $attributes, Membership $membership) => ['dependent_id' => $membership->member->dependents->random()->id])
                )
            )
            ->create();

        $members = Member::inRandomOrder()->limit(400)->get();

        Loan::each(fn($loan) => $loan->guarantors()->sync($members->random(4)));
    }
}
