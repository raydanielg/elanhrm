<?php

namespace App\Http\Middleware;

use App\Models\GlobalSetting;
use Closure;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class EmailVerified extends EnsureEmailIsVerified
{

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     */
    public function handle($request, Closure $next, $redirectToRoute = null)
    {
        $globalSetting = GlobalSetting::first();



        if (!$request->user() || ($request->user() instanceof MustVerifyEmail && !$request->user()->hasVerifiedEmail() && $globalSetting->email_verification)) {
            $message = 'Your email address is not verified.';

            $user = auth()->user()->user;

            if ($user?->is_superadmin) {
                return $next($request);
            }

            return $request->expectsJson() ? abort(403, $message) : Redirect::guest(URL::route($redirectToRoute ?: 'verification.notice'));
        }

        return $next($request);
    }

}
