<?php

use App\Models\Module;
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
        if (Schema::hasColumn('modules', 'is_superadmin')) {
            $modules = Module::withoutGlobalScopes()->get();

            Schema::table('modules', function (Blueprint $table) {
                $table->boolean('is_superadmin')->default(0)->change();
            });

            $superAdminModules = $modules->where('is_superadmin', 1)->pluck('id')->toArray();

            Module::withoutGlobalScopes()->update(['is_superadmin' => 0]);

            Module::withoutGlobalScopes()->whereIn('id', $superAdminModules)->update(['is_superadmin' => 1]);
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }

};
