<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'id'         => 1,
            'name'       => 'Chezky',
            'email'      => '2771355@gmail.com',
            'password'   => '$2y$10$GQurl5756..cH9j2kV/CJuqnnHpe5WSaN5UJPQfEH8eEbr8ZsdrS6',
            'created_at' => '2020-10-27 13:08:52',
            'updated_at' => '2020-10-28 15:59:12',
        ]);
        DB::table('users')->insert([
            'id'             => 2,
            'name'           => 'Aaron Friedman',
            'email'          => 'amf6080@gmail.com',
            'password'       => '$2y$10$H/JmzgECcDoU2NxBBXRBgersflpBWpSuCpIEkTbyhxpnXP1kqLZ1S',
            'remember_token' => '$2y$10$H/JmzgECcDoU2NxBBXRBgersflpBWpSuCpIEkTbyhxpnXP1kqLZ1S',
            'created_at'     => '2020-10-27 17:52:43',
            'updated_at'     => '2021-01-28 19:58:02',
        ]);
        DB::table('users')->insert([
            'id'         => 3,
            'name'       => 'Mordechai Merlin',
            'email'      => 'gemachhakehilos@gmail.com',
            'password'   => '$2y$10$AhEX5pHvY07oqcz92E7LgugO6liTVjpDtG4hz5wyP8XYfgkIDix3a',
            'remember_token' => '$2y$10$AhEX5pHvY07oqcz92E7LgugO6liTVjpDtG4hz5wyP8XYfgkIDix3a',
            'created_at' => '2021-03-10 16:05:18',
            'updated_at' => '2021-05-03 14:43:13'
        ]);
        DB::table('users')->insert([
            'id'         => 4,
            'name'       => 'Levy Hochhauser',
            'email'      => 'levyhoch@gmail.com',
            'password'   => '$2y$10$ibmf0Jvp7GjVxQPekVq2Qe539JAGEy7Hsw4ROLFcUGe.rHwQdc.DK',
            'created_at' => '2021-06-09 10:53:50',
            'updated_at' => '2021-06-09 10:53:50'
        ]);
    }
}
