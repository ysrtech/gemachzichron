<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('loan_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Seed initial types
        DB::table('loan_types')->insert([
            ['name' => 'Housing'],
            ['name' => 'Wedding'],
            ['name' => 'Other'],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('loan_types');
    }
};
