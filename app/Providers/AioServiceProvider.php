<?php

namespace App\Providers;

use App\Services\Aio\Aio;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class AioServiceProvider extends ServiceProvider
{
    /**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->registerAio();
        $this->registerBindings();
        $this->registerFacade();
    }
    /**
	 * Register the application bindings.
	 *
	 * @return void
	 */
	private function registerAio()
	{
		$this->app->bind('aio', function($app) {
			return new Aio($app);
		});
	}
    /**
	 * Register the vault facade without the user having to add it to the app.php file.
	 *
	 * @return void
	 */
	public function registerFacade() {
		$this->app->booting(function()
		{
			$loader = AliasLoader::getInstance();
			$loader->alias('Aio', 'App\Services\Aio\Facades\Aio');
		});
	}    
    /**
     * Register service provider bindings
     */
    public function registerBindings() 
    {            
        $this->app->bind(
        	'App\Repositories\Backend\Location\LocationContract',
		'App\Repositories\Backend\Location\EloquentLocationRepository'
	);
        $this->app->bind(
        	'App\Repositories\Backend\PLocation\PLocationContract',
		'App\Repositories\Backend\PLocation\EloquentPLocationRepository'
	);
        $this->app->bind(
        	'App\Repositories\Backend\Participant\ParticipantContract',
		'App\Repositories\Backend\Participant\EloquentParticipantRepository'
	);
        $this->app->bind(
        	'App\Repositories\Backend\Participant\Role\RoleRepositoryContract',
		'App\Repositories\Backend\Participant\Role\EloquentRoleRepository'
	);
        $this->app->bind(
        	'App\Repositories\Backend\Organization\OrganizationContract',
		'App\Repositories\Backend\Organization\EloquentOrganizationRepository'
	);
        $this->app->bind(
        	'App\Repositories\Backend\Project\ProjectContract',
		'App\Repositories\Backend\Project\EloquentProjectRepository'
	);
        $this->app->bind(
        	'App\Repositories\Backend\Question\QuestionContract',
		'App\Repositories\Backend\Question\EloquentQuestionRepository'
	);
        
        $this->app->bind(
        	'App\Repositories\Backend\Result\ResultContract',
		'App\Repositories\Backend\Result\EloquentResultRepository'
	);
        $this->app->bind(
        	'App\Repositories\Frontend\Location\LocationContract',
		'App\Repositories\Frontend\Location\EloquentLocationRepository'
	);
        $this->app->bind(
        	'App\Repositories\Frontend\PLocation\PLocationContract',
		'App\Repositories\Frontend\PLocation\EloquentPLocationRepository'
	);
        $this->app->bind(
        	'App\Repositories\Frontend\Organization\OrganizationContract',
		'App\Repositories\Frontend\Organization\EloquentOrganizationRepository'
	);
        $this->app->bind(
        	'App\Repositories\Frontend\Participant\ParticipantContract',
		'App\Repositories\Frontend\Participant\EloquentParticipantRepository'
	);
        $this->app->bind(
        	'App\Repositories\Frontend\Participant\Role\RoleRepositoryContract',
		'App\Repositories\Frontend\Participant\Role\EloquentRoleRepository'
	);
        $this->app->bind(
        	'App\Repositories\Frontend\Project\ProjectContract',
		'App\Repositories\Frontend\Project\EloquentProjectRepository'
	);
        $this->app->bind(
        	'App\Repositories\Frontend\Question\QuestionContract',
		'App\Repositories\Frontend\Question\EloquentQuestionRepository'
	);
        
        $this->app->bind(
        	'App\Repositories\Frontend\Result\ResultContract',
		'App\Repositories\Frontend\Result\EloquentResultRepository'
	);
    }
}
