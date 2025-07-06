<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{

    /**
     * Handle the event.
     *
     * @param Login $event
     * @return void
     */
    public function handle(Login $event)
    {
        // WORKSUITESAAS
        if (!session()->has('impersonate') && !session()->has('stop_impersonate')) {
            $user = $event->user->user;
            $user->last_login = now();
            /* @phpstan-ignore-line */
            $user->save();

            if (company()) {
                $company = company();
                $company->last_login = now();  /* @phpstan-ignore-line */
                $company->saveQuietly();
            }
        }
    }

}
