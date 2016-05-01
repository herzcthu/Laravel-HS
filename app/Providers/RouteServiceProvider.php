<?php namespace App\Providers;

use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider {

	/**
	 * This namespace is applied to the controller routes in your routes file.
	 *
	 * In addition, it is set as the URL generator's root namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'App\Http\Controllers';
        
        
	/**
	 * Define your route model bindings, pattern filters, etc.
	 *
	 * @param  \Illuminate\Routing\Router  $router
	 * @return void
	 */
	public function boot(Router $router)
	{
		//

		parent::boot($router);
                
                $router->model('project', 'App\Project');
                $router->model('participants', 'App\Participant');
                $router->model('projects', 'App\Project');
                $router->model('questions', 'App\Question');
                $router->model('organizations', 'App\Organization');
                //$router->model('pcode', 'App\PLocation');
                $router->bind('pcode', function($value, $route){
                    if($route->project->type == 'incident'){
                        if(\Request::ajax()){
                            if($route->project->validate == 'person'){
                                $pcode = \App\Participant::find($value);
                            }elseif($route->project->validate == 'pcode'){
                                $pcode = \App\PLocation::find($value);
                            }
                        }else{
                            $pcode = \App\Result::find($value);
                        }
                    }else{
                        if($route->project->validate == 'person'){
                            $pcode = \App\Participant::find($value);
                        }elseif($route->project->validate == 'pcode'){
                            $pcode = \App\PLocation::where('org_id', $route->project->organization->id)
                                    ->where('pcode',$value)->first();
                        }
                    }
                    if(!is_null($pcode)){
                        return $pcode;
                    }else{
                        \App::abort(404, 'Not Found.');
                    }
                });
                
                $router->bind('person', function($value, $route){ //dd($route->project);
                    $person = \App\Participant::where('participant_id', $value)->where('org_id', $route->project->org_id)->first();
                    if(!is_null($person)){
                        return $person;
                    }else{
                        \App::abort(404, 'Not Found.');
                    }
                });
	}

	/**
	 * Define the routes for the application.
	 *
	 * @param  \Illuminate\Routing\Router  $router
	 * @return void
	 */
	public function map(Router $router)
	{
		$router->group(['namespace' => $this->namespace], function($router)
		{
			require app_path('Http/routes.php');
		});
	}
}