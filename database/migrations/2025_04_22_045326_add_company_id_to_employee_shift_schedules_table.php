<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('employee_shift_schedules', 'company_id')) {
            Schema::table('employee_shift_schedules', function (Blueprint $table) {
                $table->integer('company_id')->unsigned()->nullable()->after('user_id');
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
            });
        }

        // Use direct DB query for faster updates
        DB::statement('
            UPDATE employee_shift_schedules ess
            INNER JOIN users u ON ess.user_id = u.id
            SET ess.company_id = u.company_id
            WHERE u.company_id IS NOT NULL
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_shift_schedules', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });
    }
};
