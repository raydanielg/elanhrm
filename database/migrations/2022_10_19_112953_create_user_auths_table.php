<?php

use App\Models\User;
use App\Models\UserAuth;
use App\Scopes\ActiveScope;
use App\Scopes\CompanyScope;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('user_auths')) {
            Schema::create('user_auths', function (Blueprint $table) {
                $table->id();
                $table->string('email')->unique();
                $table->index('email');
                $table->string('password');
                $table->string('remember_token')->nullable();

                $table->text('two_factor_secret')->nullable();
                $table->text('two_factor_recovery_codes')->nullable();
                $table->boolean('two_factor_confirmed')->default(false);
                $table->boolean('two_factor_email_confirmed')->default(false);
                $table->enum('two_fa_verify_via', ['email', 'google_authenticator', 'both'])->nullable();
                $table->string('two_factor_code')->nullable()->comment('when authenticator is email');
                $table->dateTime('two_factor_expires_at')->nullable();
                $table->dateTime('email_verified_at')->nullable();

                $table->timestamps();
            });

            if (!Schema::hasColumn('users', 'is_superadmin')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->boolean('is_superadmin')->default(false)->after('company_id');
                });
            }

            Schema::table('users', function (Blueprint $table) {
                $table->bigInteger('user_auth_id')->unsigned()->nullable()->after('company_id');
                $table->foreign('user_auth_id')->references('id')->on('user_auths')->onDelete('cascade')->onUpdate('cascade');
            });


            $users = User::select(['email', 'created_at', 'password', 'remember_token', 'id'])
                ->whereNotNull('email')
                ->withoutGlobalScopes([ActiveScope::class, CompanyScope::class])
                ->get();

            foreach ($users as $user) {
                $userAuth = new UserAuth();

                $userAuth->email = $user->email;
                $userAuth->password = $user->password;
                $userAuth->remember_token = $user->remember_token;
                $userAuth->two_fa_verify_via = $user->two_fa_verify_via;
                $userAuth->email_verified_at = $user->created_at;
                $userAuth->saveQuietly();

                $user->user_auth_id = $userAuth->id;
                $user->saveQuietly();
            }
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['password', 'remember_token']);

            if (Schema::hasColumn('users', 'two_factor_secret')) {
                $table->dropColumn([
                    'two_factor_secret',
                    'two_factor_recovery_codes',
                    'two_factor_confirmed',
                    'two_factor_email_confirmed',
                    'two_fa_verify_via',
                    'two_factor_code',
                    'two_factor_expires_at'
                ]);
            }
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_auths');
    }

};
