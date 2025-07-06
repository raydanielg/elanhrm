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
        if (!Schema::hasColumn('user_auths', 'twitter_id')) {
            Schema::table('user_auths', function (Blueprint $table) {
                $table->string('twitter_id')->nullable()->after('email_code_expires_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_auths', function (Blueprint $table) {
            $table->dropColumn('twitter_id');
        });
    }

};
