<?php namespace App\Repositories\Backend\User;

use App\Exceptions\Backend\Access\User\UserNeedsRolesException;
use App\Exceptions\GeneralException;
use App\Repositories\Backend\Organization\OrganizationContract;
use App\Repositories\Backend\Role\RoleRepositoryContract;
use App\Repositories\Frontend\Auth\AuthenticationContract;
use App\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Input;

/**
 * Class EloquentUserRepository
 * @package App\Repositories\User
 */
class EloquentUserRepository implements UserContract {

	/**
	 * @var RoleRepositoryContract
	 */
	protected $role;

	/**
	 * @var AuthenticationContract
	 */
	protected $auth;

        protected $organization;
        /**
	 * @param RoleRepositoryContract $role
	 * @param AuthenticationContract $auth
	 */
	public function __construct(RoleRepositoryContract $role, AuthenticationContract $auth, OrganizationContract $organization) {
		$this->role = $role;
		$this->auth = $auth;
                $this->organization = $organization;
	}

	/**
	 * @param $id
	 * @param bool $withRoles
	 * @return mixed
	 * @throws GeneralException
	 */
	public function findOrThrowException($id, $withRole = false, $withOrg = false) {
		if ($withRole)
			$user = User::with('role')->with('organization')->withTrashed()->find($id);
		else
			$user = User::withTrashed()->find($id);

		if (! is_null($user)) return $user;

		throw new GeneralException('That user does not exist.');
	}

	/**
	 * @param $per_page
	 * @param string $order_by
	 * @param string $sort
	 * @param int $status
	 * @return mixed
	 */
	public function getUsersPaginated($per_page, $status = 1, $order_by = 'id', $sort = 'asc') {
                $order_by = ((null !== Input::get('field'))? Input::get('field'):$order_by);
                $sort = ((null !== Input::get('sort'))? Input::get('sort'):$sort);
                if(!access()->user()->can('manage_organization')){ 
                    return User::where('status', $status)
                            ->where('org_id', access()->user()->organization->id)
                          ->orderBy($order_by, $sort)->paginate($per_page);
                }else{
                    return User::where('status', $status)
                          ->orderBy($order_by, $sort)->paginate($per_page);
                }
	}
        
        /**
	 * @param $per_page
	 * @param string $order_by
	 * @param string $sort
	 * @param int $status
	 * @return mixed
	 */
	public function searchUsers($queue, $status = 1, $order_by = 'id', $sort = 'asc') {
            $order_by = ((null !== Input::get('field'))? Input::get('field'):$order_by);
            $sort = ((null !== Input::get('sort'))? Input::get('sort'):$sort);
            if(!access()->user()->can('manage_organization')){ 
                    return User::where('status', $status)
                            ->where('org_id', access()->user()->organization->id)
                          ->orderBy($order_by, $sort)->search($queue)->get();
                }else{
                    return User::where('status', $status)->orderBy($order_by, $sort)->search($queue)->get();
                }
	}

	/**
	 * @param $per_page
	 * @return Paginator
	 */
	public function getDeletedUsersPaginated($per_page) {
		return User::onlyTrashed()->paginate($per_page);
	}

	/**
	 * @param string $order_by
	 * @param string $sort
	 * @return mixed
	 */
	public function getAllUsers($order_by = 'id', $sort = 'asc') {
		return User::orderBy($order_by, $sort)->get();
	}

	/**
	 * @param $input
	 * @param $roles
	 * @param $permissions
	 * @return bool
	 * @throws GeneralException
	 * @throws UserNeedsRolesException
	 */
	public function create($input, $roles, $permissions, $organizations) {
		$user = $this->createUserStub($input);
                $organization = $this->organization->findOrThrowException($organizations['users_organizations']);
                
                $role = $this->role->findOrThrowException($roles['users_roles']);
                
		if ($user->save()) {
			
			//Attach new roles
			$user->attachRoles($roles['assignees_roles']);

			//Attach other permissions
			$user->attachPermissions($permissions['permission_user']);
                        
                        //Attach other permissions
			$user->attachOrganizations($organizations['users_organizations']);

			//Send confirmation email if requested
			if (isset($input['confirmation_email']) && $user->confirmed == 0)
				$this->auth->resendConfirmationEmail($user->id);

			return true;
		}

		throw new GeneralException('There was a problem creating this user. Please try again.');
	}

	/**
	 * @param $id
	 * @param $input
	 * @param $roles
	 * @return bool
	 * @throws GeneralException
	 */
	public function update($id, $input, $roles, $permissions, $organizations) {
		$user = $this->findOrThrowException($id);
		$this->checkUserByEmail($input, $user);
                
                if(!empty($organizations['users_organization']) && $organizations['users_organization'] != 'none'){
                    $organization = $this->organization->findOrThrowException($organizations['users_organization']);
                    $this->flushOrganizations($organization, $user);
                }else{
                    $user->organization()->dissociate();
                }
                
                $rolecheck = $this->checkUserRoleLevel($roles, $user);
		if ($user->update($input)) {                      
                    //For whatever reason this just wont work in the above call, so a second is needed for now
                    $user->status = isset($input['status']) ? 1 : 0;
                    $user->confirmed = isset($input['confirmed']) ? 1 : 0;


                    $this->checkUserRolesCount($roles);
                    if($rolecheck){
                        $this->flushRoles($roles, $user);
                    }
                    $this->flushPermissions($permissions, $user);

                    $user->save();

                    return true;
		}

		throw new GeneralException('There was a problem updating this user. Please try again.');
	}

	/**
	 * @param $id
	 * @param $input
	 * @return bool
	 * @throws GeneralException
	 */
	public function updatePassword($id, $input) {
		$user = $this->findOrThrowException($id);

		//Passwords are hashed on the model
		$user->password = $input['password'];
		if ($user->save())
			return true;

		throw new GeneralException('There was a problem changing this users password. Please try again.');
	}

	/**
	 * @param $id
	 * @return bool
	 * @throws GeneralException
	 */
	public function destroy($id) {
		if (auth()->id() == $id)
			throw new GeneralException("You can not delete yourself.");

		$user = $this->findOrThrowException($id);
		if ($user->delete())
			return true;

		throw new GeneralException("There was a problem deleting this user. Please try again.");
	}

	/**
	 * @param $id
	 * @return boolean|null
	 * @throws GeneralException
	 */
	public function delete($id) {
		$user = $this->findOrThrowException($id, true);

		//Detach all roles & permissions
		$user->detachRoles($user->roles);
		$user->detachPermissions($user->permissions);

		try {
			$user->forceDelete();
		} catch (\Exception $e) {
			throw new GeneralException($e->getMessage());
		}
	}

	/**
	 * @param $id
	 * @return bool
	 * @throws GeneralException
	 */
	public function restore($id) {
		$user = $this->findOrThrowException($id);

		if ($user->restore())
			return true;

		throw new GeneralException("There was a problem restoring this user. Please try again.");
	}

	/**
	 * @param $id
	 * @param $status
	 * @return bool
	 * @throws GeneralException
	 */
	public function mark($id, $status) {
		if (auth()->id() == $id && ($status == 0 || $status == 2))
			throw new GeneralException("You can not do that to yourself.");

		$user = $this->findOrThrowException($id);
		$user->status = $status;

		if ($user->save())
			return true;

		throw new GeneralException("There was a problem updating this user. Please try again.");
	}

	/**
	 * @param $input
	 * @param $user
	 * @throws GeneralException
	 */
	private function checkUserByEmail($input, $user)
	{
		//Figure out if email is not the same
		if ($user->email != $input['email'])
		{
			//Check to see if email exists
			if (User::where('email', '=', $input['email'])->first())
				throw new GeneralException('That email address belongs to a different user.');
		}
	}

	/**
	 * @param $roles
	 * @param $user
	 */
	private function flushRoles($role, $user)
	{
                if(is_object($role))
                    $update_role = $role;
                
                if(is_array($role))
                    $update_role = $this->role->findOrThrowException($role['user_role']);
                
                if(is_int($role))
                    $update_role = $this->role->findOrThrowException($role);
		$user->role()->dissociate();
		$user->role()->associate($update_role);
	}

	/**
	 * @param $permissions
	 * @param $user
	 */
	private function flushPermissions($permissions, $user)
	{
		//Flush permissions out, then add array of new ones if any
		$user->detachPermissions($user->permissions);
		if (count($permissions['permission_user']) > 0)
			$user->attachPermissions($permissions['permission_user']);
	}
        
        /**
	 * @param $organizations
	 * @param $user
	 */
	private function flushOrganizations($organization, $user)
	{
		$user->organization()->dissociate();
		$user->organization()->associate($organization);
	}

	/**
	 * @param $roles
	 * @throws GeneralException
	 */
	private function checkUserRolesCount($roles)
	{
		//User Updated, Update Roles
		//Validate that there's at least one role chosen
		if (count($roles['user_role']) == 0)
			throw new GeneralException('You must choose at least one role.');
	}
        
        private function checkUserRoleLevel($role, $user) {
            if(is_object($role))
                    $update_role = $role;
                
                if(is_array($role))
                    $update_role = $this->role->findOrThrowException($role['user_role']);
                
                if(is_int($role))
                    $update_role = $this->role->findOrThrowException($role);
                
            $current_role = -1 * auth()->user()->role->level;
            $editing_role = -1 * $update_role->level;
            if (auth()->id() == $user->id &&  auth()->user()->role->id != $update_role->id){
                throw new GeneralException('You cannot change role yourself to another role.');
            }elseif($current_role < $editing_role){
                throw new GeneralException('You have no permission to change higher role.');
            }else{
                return true;
            }
        }



        /**
	 * @param $input
	 * @return mixed
	 */
	private function createUserStub($input)
	{
		$user = new User;
		$user->name = $input['name'];
		$user->email = $input['email'];
		$user->password = $input['password'];
		$user->status = isset($input['status']) ? 1 : 0;
		$user->confirmation_code = md5(uniqid(mt_rand(), true));
		$user->confirmed = isset($input['confirmed']) ? 1 : 0;
		return $user;
	}
}