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
        Schema::table('user_auths', function (Blueprint $table) {
            $table->string('email_verification_code')->nullable()->after('email_verified_at');
            $table->dateTime('email_code_expires_at')->nullable()->after('email_verification_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_auths', function (Blueprint $table) {
            $table->dropColumn(['email_verification_code', 'email_code_expires_at']);
        });
    }

};
