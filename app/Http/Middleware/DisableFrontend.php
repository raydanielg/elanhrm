<?php

namespace App\Http\Middleware;

use Closure;

class DisableFrontend
{

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $global = global_setting();

        if ($global->frontend_disable && request()->route()->getName() != 'front.signup.index' && !request()->ajax()) {
            return redirect(route('login'));
        }

        return $next($request);
    }

}
