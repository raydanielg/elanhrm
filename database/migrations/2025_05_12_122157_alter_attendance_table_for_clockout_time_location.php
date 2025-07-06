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
        Schema::table('attendances', function (Blueprint $table) {
            $table->enum('clock_out_time_work_from_type', ['home', 'office', 'other'])->nullable();
            $table->unsignedBigInteger('clock_out_time_location_id')->nullable()->index('attendances_clock_out_time_location_id_foreign');
            $table->foreign(['clock_out_time_location_id'])->references(['id'])->on('company_addresses')->onUpdate('CASCADE')->onDelete('SET NULL');
            $table->string('clock_out_time_working_from')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('clock_out_time_work_from_type');
            $table->dropColumn('clock_out_time_location_id');
            $table->dropColumn('clock_out_time_working_from');
        });
    }
};
