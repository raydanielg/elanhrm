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
        Schema::table('project_template_tasks', function (Blueprint $table) {
            $table->unsignedInteger('milestone_id')->nullable()->after('project_template_id')->index('tasks_milestone_id_foreign');
            $table->foreign(['milestone_id'])->references(['id'])->on('project_template_milestone')->onUpdate('CASCADE')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_template_tasks', function (Blueprint $table) {
            $table->dropForeign(['milestone_id']);
            $table->dropColumn('milestone_id');
        });
    }
};
