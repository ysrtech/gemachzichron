<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained();
            $table->string('gateway');
            $table->string('gateway_identifier')->nullable();
            $table->string('type');
            $table->float('amount');
            $table->date('start_date');
            $table->integer('installments')->nullable();
            $table->string('frequency');
            $table->text('comment')->nullable();
            $table->float('membership_fee')->default(0);
            $table->float('processing_fee')->default(0);
            $table->float('decline_fee')->default(0);
            $table->boolean('active')->default(true);
            $table->text('data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscriptions');
    }
}
