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

         Schema::create('project_sub_categories', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedInteger('category_id')->index('client_sub_categories_category_id_foreign');
                $table->foreign(['category_id'])->references(['id'])->on('project_category')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->string('category_name');
                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_subcategory');
    }
};
