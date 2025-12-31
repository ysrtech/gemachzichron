<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->string('loan_type')->nullable()->after('id');
        });

        // Assign loan types to existing loans based on amount
        // If amount = 10000 or 18000, set to Housing
        // Otherwise, set to Wedding
        DB::table('loans')
            ->whereNull('loan_type')
            ->update([
                'loan_type' => DB::raw("CASE WHEN amount IN (10000, 18000) THEN 'Housing' ELSE 'Wedding' END")
            ]);
    }

    public function down()
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn('loan_type');
        });
    }
};
