<?php

namespace App\Providers;

use App\Models\Model;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Schema;
use App\Models\Permission;
use Illuminate\Contracts\Auth\Guard;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Check boot method is loaded or not.
     *
     * @var boolean
     */
    public $isBooted;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Guard $auth)
    {
        Schema::defaultStringLength(191);

        // Will be used to prevent lazy loading (N+1 problem) in local environment
        // This will removed later
        Model::preventLazyLoading(! app()->isProduction());

        error_reporting(E_ALL);
        if (!$this->app->runningInConsole() && env('APP_INSTALL') == true) {
            View::composer('*', function ($view) use ($auth) {
                if (!$this->isBooted) {
                    $json = \Cache::get('lanObject-' . config('app.locale'));
                    if (empty($json)) {
                        $json = file_get_contents(resource_path('lang/' . config('app.locale') . '.json'));
                        \Cache::put('lanObject-' . config('app.locale'), $json, 86400);
                    }
                    $data['json'] = $json;
                    $data['prms'] = Permission::getAuthUserPermission(optional($auth->user())->id);
                    $data['accessToken'] = !empty($auth->user()) && empty($auth->user()->token()) ? $auth->user()->createToken('accessToken')->accessToken : '';
                    $view->with($data);
                    $this->isBooted = true;
                }
            });
        }
    }
}
