<?php namespace App\Http\Controllers\Backend\Access;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Access\User\BulkUpdateParticipantsRequest;
use App\Http\Requests\Backend\Access\User\CreateUserRequest;
use App\Http\Requests\Backend\Access\User\UpdateUserPasswordRequest;
use App\Http\Requests\Backend\Access\User\UpdateUserRequest;
use App\Repositories\Backend\Organization\OrganizationContract;
use App\Repositories\Backend\Permission\PermissionRepositoryContract;
use App\Repositories\Backend\Role\RoleRepositoryContract;
use App\Repositories\Backend\User\UserContract;
use App\Repositories\Frontend\Auth\AuthenticationContract;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;

/**
 * Class UserController
 */
class UserController extends Controller {

	/**
	 * @var UserContract
	 */
	protected $users;
	/**
	 * @var RoleRepositoryContract
	 */
	protected $roles;

	/**
	 * @var PermissionRepositoryContract
	 */
	protected $permissions;
        
        /**
	 * @var OrganizationContract
	 */
	protected $organizations;

	/**
	 * @param UserContract $users
	 * @param RoleRepositoryContract $roles
	 * @param PermissionRepositoryContract $permissions
         * @param OrganizationContract $organizations
	 */
	public function __construct(
		UserContract $users,
		RoleRepositoryContract $roles,
		PermissionRepositoryContract $permissions, 
                OrganizationContract $organizations) {
		$this->users = $users;
		$this->roles = $roles;
		$this->permissions = $permissions;
                $this->organizations = $organizations;
	}

	/**
	 * @return mixed
	 */
	public function index() {
		return view('backend.access.index')
			->withUsers($this->users->getUsersPaginated(config('access.users.default_per_page'), 1))
                        ->withRoles($this->roles->getAllRoles('id', 'asc', true));
	}

	/**
	 * @return mixed
	 */
	public function create() {
		return view('backend.access.create')
			->withRoles($this->roles->getAllRoles('id', 'asc', true))
			->withPermissions($this->permissions->getPermissionsNotAssociatedWithRole())
                        ->withOrganizations($this->organizations->getAllOrganizations('name', 'asc'));
	}

	/**
	 * @param CreateUserRequest $request
	 * @return mixed
	 */
	public function store(CreateUserRequest $request) {
		$this->users->create(
			$request->except('assignees_roles', 'permission_user', 'users_organization'),
			$request->only('assignees_roles'),
			$request->only('permission_user'),
                        $request->only('users_organization')
		);
		return redirect()->route('admin.access.users.index')->withFlashSuccess('The user was successfully created.');
	}

	/**
	 * @param $id
	 * @return mixed
	 */
	public function edit($id, Request $request) {
            $view = View::make('includes.partials.medialist_grid');
            $user = $this->users->findOrThrowException($id, true);
            if ($request->ajax()) {

               $sections = $view->renderSections(); 

                return json_encode($sections['mediagrid']);
                    //return Response::json(view('', compact('posts'))->render());
            } else {
            return view('backend.access.edit')
                    ->withUser($user)
                    ->withRoles($this->roles->getAllRoles('id', 'asc', true))
                    ->withUserPermissions($user->permissions->lists('id')->all())
                    ->withPermissions($this->permissions->getPermissionsNotAssociatedWithRole())
                    ->withOrganizations($this->organizations->getAllOrganizations('name', 'asc'));
            }
	}

	/**
	 * @param $id
	 * @param UpdateUserRequest $request
	 * @return mixed
	 */
	public function update($id, UpdateUserRequest $request) {            
		$this->users->update($id,
			$request->except('user_role', 'permission_user', 'users_organization'),
			$request->only('user_role'),
			$request->only('permission_user'),
                        $request->only('users_organization')
		);
		return redirect()->route('admin.access.users.index')->withFlashSuccess('The user was successfully updated.');
	}

	/**
	 * @param $id
	 * @return mixed
	 */
	public function destroy($id) {
		$this->users->destroy($id);
		return redirect()->back()->withFlashSuccess('The user was successfully deleted.');
	}

	/**
	 * @param $id
	 * @return mixed
	 */
	public function delete($id) {
		$this->users->delete($id);
		return redirect()->back()->withFlashSuccess('The user was deleted permanently.');
	}

	/**
	 * @param $id
	 * @return mixed
	 */
	public function restore($id) {
		$this->users->restore($id);
		return redirect()->back()->withFlashSuccess('The user was successfully restored.');
	}

	/**
	 * @param $id
	 * @param $status
	 * @return mixed
	 */
	public function mark($id, $status) {
		$this->users->mark($id, $status);
		return redirect()->back()->withFlashSuccess('The user was successfully updated.');
	}

	/**
	 * @return mixed
	 */
	public function deactivated() {
		return view('backend.access.deactivated')
			->withUsers($this->users->getUsersPaginated(25, 0));
	}

	/**
	 * @return mixed
	 */
	public function deleted() {
		return view('backend.access.deleted')
			->withUsers($this->users->getDeletedUsersPaginated(25));
	}

	/**
	 * @return mixed
	 */
	public function banned() {
		return view('backend.access.banned')
			->withUsers($this->users->getUsersPaginated(25, 2));
	}

	/**
	 * @param $id
	 * @return mixed
	 */
	public function changePassword($id) {
		return view('backend.access.change-password')
			->withUser($this->users->findOrThrowException($id));
	}

	/**
	 * @param $id
	 * @param UpdateUserPasswordRequest $request
	 * @return mixed
	 */
	public function updatePassword($id, UpdateUserPasswordRequest $request) {
		$this->users->updatePassword($id, $request->all());
		return redirect()->route('admin.access.users.index')->withFlashSuccess("The user's password was successfully updated.");
	}

	/**
	 * @param $user_id
	 * @param AuthenticationContract $auth
	 * @return mixed
	 */
	public function resendConfirmationEmail($user_id, AuthenticationContract $auth) {
		$auth->resendConfirmationEmail($user_id);
		return redirect()->back()->withFlashSuccess("A new confirmation e-mail has been sent to the address on file.");
	}
        
        
        public function bulk(BulkUpdateParticipantsRequest $request) {
            //dd($this->users);
            /**
             * To Do: check if user is allowed to update roles.
             * Or current user is above the updating roles
             */
            $role = (int) $request->role;
            foreach($request->users as $id => $status) {
                $users = $this->users->findOrThrowException($id, true);
                $user['name'] = $users->name;
                $user['status'] = $users->status;
                $user['confirmed'] = $users->confirmed;
                $user['email'] = $users->email;
                $roles = $users->roles->lists('id')->all();
                $permissions = $users->permissions->lists('id')->all();
                //dd($role);
                array_push($roles, $role);
                //dd($roles);
                $this->users->update($id, $user, ['assignees_roles' => array_unique(array($role))], ['permission_user' => $permissions]);
            }
            
            return redirect()->route('admin.access.users.index')->withFlashSuccess('The users were successfully updated.');
        }
        
        /**
	 * @return mixed
	 */
	public function search() {
                $query = Input::get('q');
                $order_by = ((null !== Input::get('field'))? Input::get('field'): 'id');
                $sort = ((null !== Input::get('sort'))? Input::get('sort'): 'asc');
                $user = $this->users->searchUsers($query, true, $order_by, $sort);
                $total = $user->count();
                $pageName = 'page';
                $per_page = config('access.users.default_per_page');
                $page = null;
                //Create custom pagination
                $users = new LengthAwarePaginator($user, $total, $per_page, $page, [
                                    'path' => Paginator::resolveCurrentPath(),
                                    'pageName' => $pageName,
                                ]);
                if($users->count() == 0){
                    return redirect()->route('admin.access.users.index')->withFlashDanger('Your search term "'.$query.'" not found!');
                }
		return view('backend.access.index', compact('users'))
                        ->withRoles($this->roles->getAllRoles('id', 'asc', true));
	}
}