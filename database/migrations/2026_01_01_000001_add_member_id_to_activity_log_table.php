<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMemberIdToActivityLogTable extends Migration
{
    public function up()
    {
        Schema::table('activity_log', function (Blueprint $table) {
            $table->unsignedBigInteger('member_id')->nullable()->after('causer_id');
            $table->index('member_id');
        });
    }

    public function down()
    {
        Schema::table('activity_log', function (Blueprint $table) {
            $table->dropIndex(['member_id']);
            $table->dropColumn('member_id');
        });
    }
}
