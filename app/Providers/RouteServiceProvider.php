<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{

    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/account/dashboard';

    public const SUPER_ADMIN_HOME = '/account/super-admin-dashboard';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {

            $this->mapPublicRoutes();
            $this->mapSettingRoutes();
            $this->mapApiRoutes();
            $this->mapWebRoutes();

            // WORKSUITESAAS
            if (isWorksuiteSaas()) {
                $this->mapSuperAdminRoutes();
                $this->mapSuperAdminPublicRoutes();
            }
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware(['web', 'check-company-package', 'auto-logout'])
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }

    /**
     * Define the "admin" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapPublicRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web-public.php'));
    }

    /**
     * Define the "admin" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapSettingRoutes()
    {
        Route::middleware(['web', 'check-company-package', 'auto-logout'])
            ->namespace($this->namespace)
            ->group(base_path('routes/web-settings.php'));
    }

    /**
     * Define the "super admin" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    // WORKSUITESAAS
    protected function mapSuperAdminRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/SuperAdmin/web.php'));
    }

    protected function mapSuperAdminPublicRoutes()
    {
        Route::namespace($this->namespace)
            ->group(base_path('routes/SuperAdmin/web-public.php'));
    }

}
