<?php

namespace Database\Seeders;

use App\Models\PlanType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanTypeRatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Plan types 1-7: $120 (2019-03-06) then $190 (2024-07-19)
        foreach ([1, 2, 3, 4, 6, 7] as $planTypeId) {
            $planType = PlanType::find($planTypeId);
            if ($planType) {
                $planType->rates = [
                    '2019-03-06' => 120,
                    '2024-07-19' => 190,
                ];
                $planType->save();
            }
        }

        // Plan types 10-12: $120 (2019-03-06) only
        foreach ([10, 11, 12] as $planTypeId) {
            $planType = PlanType::find($planTypeId);
            if ($planType) {
                $planType->rates = [
                    '2019-03-06' => 120,
                ];
                $planType->save();
            }
        }

        // Plan type 15: $95 (2019-03-06)
        $planType = PlanType::find(15);
        if ($planType) {
            $planType->rates = [
                '2019-03-06' => 95,
            ];
            $planType->save();
        }
    }
}
