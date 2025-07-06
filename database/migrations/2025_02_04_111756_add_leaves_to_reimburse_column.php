<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employee_leave_quotas', function (Blueprint $table) {
            $table->integer('leaves_to_reimburse')->default(0)->after('carry_forward_applied'); // This is the number of leaves that are to be reimbursed. Set this to 0 after reimbursing the leaves.
            $table->integer('leaves_actually_reimbursed')->default(0)->after('leaves_to_reimburse'); // This is the actual number of leaves that have been reimbursed. This is just for the record.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_leave_quotas', function (Blueprint $table) {
            $table->dropColumn('leaves_to_reimburse');
            $table->dropColumn('leaves_actually_reimbursed');
        });
    }
};
