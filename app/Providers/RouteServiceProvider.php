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
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/dashboard';


    /**
     * The controller namespace for the application.
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    protected $namespace = 'App\\Http\\Controllers';
    protected $siteNamespace = 'App\\Http\\Controllers\\Site';
    protected $userNamespace = 'App\\Http\\Controllers\\User';
    protected $apiNamespace = 'App\\Http\\Controllers\\Api';


    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('admin')
                ->middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));

            Route::middleware('web')
                ->namespace($this->siteNamespace)
                ->group(base_path('routes/site.php'));

            Route::middleware('web')
                ->namespace($this->userNamespace)
                ->group(base_path('routes/user.php'));

            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->apiNamespace)
                ->group(base_path('routes/api.php'));

            foreach ($this->app['modules']->allEnabled() as $module) {
                if (file_exists(module_path($module->getName(), '/Routes/web.php'))) {
                    Route::middleware('web')
                        ->group(module_path($module->getName(), '/Routes/web.php'));
                }

                if (file_exists(module_path($module->getName(), '/Routes/api.php'))) {
                    Route::prefix('api')
                        ->middleware('api')
                        ->group(module_path($module->getName(), '/Routes/api.php'));
                }
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
}
