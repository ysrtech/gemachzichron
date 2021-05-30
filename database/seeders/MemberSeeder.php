<?php

namespace Database\Seeders;

use App\Models\Loan;
use App\Models\Member;
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
            ->has(Loan::factory()
                ->count(2)
                ->state(fn(array $attributes, Member $member) => ['dependent_id' => $member->dependents->random()->id])
            )
            ->create();

        $members = Member::inRandomOrder()->limit(400)->get();

        Loan::each(fn($loan) => $loan->guarantors()->sync($members->random(4)));
    }
}
