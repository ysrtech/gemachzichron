<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Eliezer',
            'email' => '2771355@gmail.com',
            'password' => '11111111'
        ]);

        // \App\Models\User::factory(10)->create();
    }
}
