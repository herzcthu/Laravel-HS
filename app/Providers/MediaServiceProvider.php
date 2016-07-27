<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MediaServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        $this->model_composer();
        
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->registerBindings();
    }
    
    public function model_composer() {        
        view()->composer('includes.partials.medialist_grid', 'App\Services\Media\Media');
    }
    
    /**
	 * Register service provider bindings
	 */
	public function registerBindings() {
            
                $this->app->bind(
			'App\Repositories\Backend\Media\MediaContract',
			'App\Repositories\Backend\Media\EloquentMediaRepository'
		);
        }
}
