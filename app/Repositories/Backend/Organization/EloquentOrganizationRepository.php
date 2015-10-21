<?php namespace App\Repositories\Backend\Organization;

use App\Exceptions\Backend\Access\Organization\OrganizationNeedsRolesException;
use App\Exceptions\GeneralException;
use App\Repositories\Backend\Role\RoleRepositoryContract;
use App\Repositories\Frontend\Auth\AuthenticationContract;
use App\Organization;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Input;

/**
 * Class EloquentOrganizationRepository
 * @package App\Repositories\Organization
 */
class EloquentOrganizationRepository implements OrganizationContract {

	/**
	 * @var RoleRepositoryContract
	 */
	protected $role;

	/**
	 * @var AuthenticationContract
	 */
	protected $auth;

	/**
	 * @param RoleRepositoryContract $role
	 * @param AuthenticationContract $auth
	 */
	public function __construct(RoleRepositoryContract $role, AuthenticationContract $auth) {
		$this->role = $role;
		$this->auth = $auth;
	}

	/**
	 * @param $id
	 * @param bool $withRoles
	 * @return mixed
	 * @throws GeneralException
	 */
	public function findOrThrowException($id, $withRoles = false) {
		if ($withRoles)
			$organization = Organization::with('roles')->withTrashed()->find($id);
		else
			$organization = Organization::find($id);

		if (! is_null($organization)) return $organization;

		throw new GeneralException('That organization does not exist.');
	}

	/**
	 * @param $per_page
	 * @param string $order_by
	 * @param string $sort
	 * @param int $status
	 * @return mixed
	 */
	public function getOrganizationsPaginated($per_page, $status = 1, $order_by = 'id', $sort = 'asc') {
            $order_by = ((null !== Input::get('field'))? Input::get('field'):$order_by);
            $sort = ((null !== Input::get('sort'))? Input::get('sort'):$sort);
            if(!access()->user()->can('manage_organization')){
                return Organization::where('id', access()->user()->organization->id)->orderBy($order_by, $sort)->paginate($per_page);
            }else{
                return Organization::orderBy($order_by, $sort)->paginate($per_page);
            }
	}
        
        /**
	 * @param $per_page
	 * @param string $order_by
	 * @param string $sort
	 * @param int $status
	 * @return mixed
	 */
	public function searchOrganizations($queue, $status = 1, $order_by = 'id', $sort = 'asc') {
            $order_by = ((null !== Input::get('field'))? Input::get('field'):$order_by);
            $sort = ((null !== Input::get('sort'))? Input::get('sort'):$sort);
            if(!access()->user()->can('manage_organization')){
                return Organization::where('id', access()->user()->organization->id)->orderBy($order_by, $sort)->search($queue)->get();
            }else{
                return Organization::orderBy($order_by, $sort)->search($queue)->get();
            }
	}

	/**
	 * @param $per_page
	 * @return Paginator
	 */
	public function getDeletedOrganizationsPaginated($per_page) {
		return Organization::onlyTrashed()->paginate($per_page);
	}

	/**
	 * @param string $order_by
	 * @param string $sort
	 * @return mixed
	 */
	public function getAllOrganizations($order_by = 'id', $sort = 'asc', $with = []) {
            
            if(!access()->user()->can('manage_organization')){
		$organizations = Organization::where('id', access()->user()->organization->id)->orderBy($order_by, $sort);
            }else{
                $organizations = Organization::orderBy($order_by, $sort);
            }
            if(!empty($with)){
                return $organizations->with($with)->get();
            }else{
                return $organizations->get();
            }
	}

	/**
	 * @param $input
	 * @param $roles
	 * @param $permissions
	 * @return bool
	 * @throws GeneralException
	 * @throws OrganizationNeedsRolesException
	 */
	public function create($input) {
		$organization = $this->createOrganizationStub($input);

		if ($organization->save()) {
			
			return true;
		}

		throw new GeneralException('There was a problem creating this organization. Please try again.');
	}

	/**
	 * @param $id
	 * @param $input
	 * @param $roles
	 * @return bool
	 * @throws GeneralException
	 */
	public function update($id, $input, $roles, $permissions) {
		$organization = $this->findOrThrowException($id);
		//$this->checkOrganizationByEmail($input, $organization);
                //dd(\Illuminate\Support\Facades\Input::file());
                
                
		if ($organization->update($input)) {                      

			//For whatever reason this just wont work in the above call, so a second is needed for now
			$organization->status = isset($input['status']) ? 1 : 0;
			$organization->confirmed = isset($input['confirmed']) ? 1 : 0;
			$organization->save();

			$this->checkOrganizationRolesCount($roles);
			$this->flushRoles($roles, $organization);
			$this->flushPermissions($permissions, $organization);

			return true;
		}

		throw new GeneralException('There was a problem updating this organization. Please try again.');
	}

	/**
	 * @param $id
	 * @param $input
	 * @return bool
	 * @throws GeneralException
	 */
	public function updatePassword($id, $input) {
		$organization = $this->findOrThrowException($id);

		//Passwords are hashed on the model
		$organization->password = $input['password'];
		if ($organization->save())
			return true;

		throw new GeneralException('There was a problem changing this organizations password. Please try again.');
	}

	/**
	 * @param $id
	 * @return bool
	 * @throws GeneralException
	 */
	public function destroy($id) {
		if (auth()->id() == $id)
			throw new GeneralException("You can not delete yourself.");

		$organization = $this->findOrThrowException($id);
		if ($organization->delete())
			return true;

		throw new GeneralException("There was a problem deleting this organization. Please try again.");
	}

	/**
	 * @param $id
	 * @return boolean|null
	 * @throws GeneralException
	 */
	public function delete($id) {
		$organization = $this->findOrThrowException($id, true);

		//Detach all roles & permissions
		$organization->detachRoles($organization->roles);
		$organization->detachPermissions($organization->permissions);

		try {
			$organization->forceDelete();
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
		$organization = $this->findOrThrowException($id);

		if ($organization->restore())
			return true;

		throw new GeneralException("There was a problem restoring this organization. Please try again.");
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

		$organization = $this->findOrThrowException($id);
		$organization->status = $status;

		if ($organization->save())
			return true;

		throw new GeneralException("There was a problem updating this organization. Please try again.");
	}

	/**
	 * Check to make sure at lease one role is being applied or deactivate organization
	 * @param $organization
	 * @param $roles
	 * @throws OrganizationNeedsRolesException
	 */
	private function validateRoleAmount($organization, $roles) {
		//Validate that there's at least one role chosen, placing this here so
		//at lease the organization can be updated first, if this fails the roles will be
		//kept the same as before the organization was updated
		if (count($roles) == 0) {
			//Deactivate organization
			$organization->status = 0;
			$organization->save();

			$exception = new OrganizationNeedsRolesException();
			$exception->setValidationErrors('You must choose at lease one role. Organization has been created but deactivated.');

			//Grab the organization id in the controller
			$exception->setOrganizationID($organization->id);
			throw $exception;
		}
	}

	/**
	 * @param $input
	 * @param $organization
	 * @throws GeneralException
	 */
	private function checkOrganizationByEmail($input, $organization)
	{
		//Figure out if email is not the same
		if ($organization->email != $input['email'])
		{
			//Check to see if email exists
			if (Organization::where('email', '=', $input['email'])->first())
				throw new GeneralException('That email address belongs to a different organization.');
		}
	}

	/**
	 * @param $roles
	 * @param $organization
	 */
	private function flushRoles($roles, $organization)
	{
		//Flush roles out, then add array of new ones
		$organization->detachRoles($organization->roles);
		$organization->attachRoles($roles['assignees_roles']);
	}

	/**
	 * @param $permissions
	 * @param $organization
	 */
	private function flushPermissions($permissions, $organization)
	{
		//Flush permissions out, then add array of new ones if any
		$organization->detachPermissions($organization->permissions);
		if (count($permissions['permission_organization']) > 0)
			$organization->attachPermissions($permissions['permission_organization']);
	}

	/**
	 * @param $roles
	 * @throws GeneralException
	 */
	private function checkOrganizationRolesCount($roles)
	{
		//Organization Updated, Update Roles
		//Validate that there's at least one role chosen
		if (count($roles['assignees_roles']) == 0)
			throw new GeneralException('You must choose at least one role.');
	}

	/**
	 * @param $input
	 * @return mixed
	 */
	private function createOrganizationStub($input)
	{
		$organization = new Organization;
		$organization->name = $input['name'];
		return $organization;
	}
}