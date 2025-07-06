<?php

use App\Models\Project;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Schema::table('task_settings', function (Blueprint $table) {
            $table->enum('project_required', ['yes', 'no'])->default('no');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('task_settings', function (Blueprint $table) {
            $table->dropColumn('project_required');
        });
    }

};
