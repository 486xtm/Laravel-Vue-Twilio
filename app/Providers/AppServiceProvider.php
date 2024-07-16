<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        setlocale(LC_ALL, "pt_BR.UTF-8");
        Carbon::setLocale(config('app.locale')); // sv
        
        if (!\App::environment('local')) {
            $this->app['request']->server->set('HTTPS','on');
        }
        
    }
}
