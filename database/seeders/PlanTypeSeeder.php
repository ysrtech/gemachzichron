<?php

namespace Database\Seeders;

use App\Models\PlanType;
use Illuminate\Database\Seeder;

class PlanTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PlanType::insert([
            ['name' => 'Regular Member'],
            ['name' => '5 Year Plan'],
            ['name' => '7 Year Plan'],
            ['name' => 'Horues Shu']
        ]);
    }
}
