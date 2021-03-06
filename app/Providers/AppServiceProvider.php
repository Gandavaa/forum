<?php

namespace App\Providers;

use App\Channel;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if( $this->app->isLocal()){
            $this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
        }

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \View::composer('*', function($view){

            $channels = \Cache::rememberForever('channels', function () {
                return Channel::all();                
            });
            
            $view->with('channels', $channels);
        });

        //#53 spam free validator added
        \Validator::extend('spamfree', 'App\Rules\SpamFree@passes');
    }
}
