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
        Schema::table('project_templates', function (Blueprint $table) {
            $table->unsignedBigInteger('sub_category_id')->nullable()->after('category_id');
            $table->foreign(['sub_category_id'])->references(['id'])->on('project_sub_categories')->onUpdate('CASCADE')->onDelete('SET NULL');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_templates', function (Blueprint $table) {
            $table->dropColumn('sub_category_id');
        });
    }
};
