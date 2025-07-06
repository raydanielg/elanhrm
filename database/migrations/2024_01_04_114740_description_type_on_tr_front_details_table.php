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
        Schema::whenTableHasColumn('tr_front_details', 'price_description', function (Blueprint $table) {
            $table->text('price_description')->nullable()->change();
            $table->text('feature_description')->nullable()->change();
        });
    }

};
