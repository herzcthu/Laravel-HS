<?php namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\User\UpdateProfileRequest;
use App\Repositories\Frontend\User\UserContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

/**
 * Class ProfileController
 * @package App\Http\Controllers\Frontend
 */
class ProfileController extends Controller {

	public function index() {
            $user = auth()->user();
            return view('frontend.user.dashboard')
			->withUser($user);
        }
        /**
	 * @param $id
	 * @return mixed
	 */
	public function edit($id, Request $request) {
            $view = View::make('includes.partials.medialist_grid');

            if ($request->ajax()) {
                    
                   $sections = $view->renderSections(); 
                   
                    return json_encode($sections['mediagrid']);
			//return Response::json(view('', compact('posts'))->render());
                } else {
		return view('frontend.user.profile.edit')
			->withUser(auth()->user($id));
                }
	}

	/**
	 * @param $id
	 * @param UserContract $user
	 * @param UpdateProfileRequest $request
	 * @return mixed
	 */
	public function update($id, UserContract $user, UpdateProfileRequest $request) {
		$user->updateProfile($id, $request->all());
		return redirect()->route('frontend.dashboard')->withFlashSuccess("Profile successfully updated.");
	}
}