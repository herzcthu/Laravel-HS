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
            }elseif($user->organization && $user->role->name == 'Data Clerk'){
                return redirect(route('data.projects.index'));
            } else {
                return view('frontend.user.dashboard')
			->withUser($user);
            }    
	}
}