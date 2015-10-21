<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;


class FormServiceProvider extends ServiceProvider
{    
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        $this->registerForm();
        $this->loginForm();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    public function registerForm() {
        
        view()->composer('frontend.includes.header', 'App\Services\Forms\RegisterForm');
    }
    
    public function loginForm() {
        
        view()->composer('frontend.includes.header', 'App\Services\Forms\LoginForm');
    }

}
