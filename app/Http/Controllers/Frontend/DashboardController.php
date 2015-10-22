<?php namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

/**
 * Class DashboardController
 * @package App\Http\Controllers\Frontend
 */
class DashboardController extends Controller {

	/**
	 * @return mixed
	 */
	public function index()
	{
            $user = auth()->user();
        //dd($user->organization);
        //dd($user->organization->projects->first()->id);
        //dd($user->organization);
            if($user->can('view_backend')){
                return redirect(route('backend.dashboard'));
            }elseif($user->organization && $user->role->name == 'Data Clerk' && $user->organization->projects->count() > 1){
                //dd($user->organization->projects);
                return redirect(route('data.projects.index'));
            }elseif($user->organization && $user->organization->projects->count() == 1 && $user->role->name == 'Data Clerk'){
                return redirect(route('data.project.results.index', [$user->organization->projects->first()->id]));
            } else {
                return view('frontend.user.dashboard')
			->withUser($user);
            }    
	}
}