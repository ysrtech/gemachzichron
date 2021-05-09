<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('first_name')->index();
            $table->string('last_name')->index();
            $table->string('hebrew_first_name')->index();
            $table->string('hebrew_last_name')->index();
            $table->string('wife_name')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('email')->nullable()->index();
            $table->string('home_phone')->nullable();
            $table->string('mobile_phone')->nullable();
            $table->string('wife_mobile_phone')->nullable();
            $table->string('shtibel')->nullable();
            $table->string('father')->nullable();
            $table->string('father_in_law')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['last_name', 'first_name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('members');
    }
}
