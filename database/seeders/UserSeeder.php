<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create super admin
        User::create([
            'name' => 'Eliezer',
            'email' => '2771355@gmail.com',
            'role' => User::ROLE_SUPER_ADMIN,
            'password' => Hash::make('11111111')
        ]);
    }
}
