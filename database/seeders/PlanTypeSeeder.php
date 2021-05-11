<?php

namespace Database\Seeders;

use App\Models\PlanType;
use Illuminate\Database\Seeder;

class PlanTypeSeeder extends Seeder
{
    private $planTypes = [
        ['name' => 'Regular Member'],
        ['name' => '18'],
        ['name' => 'Horues Shu'],
        ['name' => 'New'],
        ['name' => '5 Year Plan'],
        ['name' => '7 Year Plan'],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->planTypes as $planType) {
            PlanType::updateOrCreate($planType);
        }
    }
}
