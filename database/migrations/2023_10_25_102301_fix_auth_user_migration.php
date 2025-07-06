<?php

use App\Models\User;
use App\Models\UserAuth;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $users = User::withoutGlobalScopes()->with('userAuth')->get();

        foreach ($users as $user) {

            if(!$user->email && !$user->user_auth_id && !$user->clientDetails) {
                $user->delete();
                continue;
            }

            if ($user->userAuth?->email != $user->email || $user->user_auth_id != $user->userAuth?->id) {
                $userAuth = UserAuth::where('email', $user->email)->first();

                if ($userAuth) {
                    $user->user_auth_id = $userAuth->id;
                    $user->save();
                }
            }

        }


        UserAuth::doesntHave('users')->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }

};
