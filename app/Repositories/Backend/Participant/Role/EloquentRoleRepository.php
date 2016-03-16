<?php namespace App\Repositories\Backend\Participant\Role;

use App\ParticipantRole as Role;
use App\Exceptions\GeneralException;

/**
 * Class EloquentRoleRepository
 * @package App\Repositories\Role
 */
class EloquentRoleRepository implements RoleRepositoryContract {

	/**
	 * @param $id
	 * @param bool $withPermissions
	 * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|\Illuminate\Support\Collection|null|static
	 * @throws GeneralException
	 */
	public function findOrThrowException($id, $withPermissions = false) {
		if (! is_null(Role::find($id))) {
			return Role::find($id);
		}

		throw new GeneralException('That role does not exist.');
	}
        
        public function getRoleLevel($id) {
            $role = $this->findOrThrowException($id);
            
            return $role->level;
        }

	/**
	 * @param $per_page
	 * @param string $order_by
	 * @param string $sort
	 * @return mixed
	 */
	public function getRolesPaginated($per_page, $order_by = 'id', $sort = 'asc') {
		return Role::orderBy($order_by, $sort)->paginate($per_page);
	}

	/**
	 * @param string $order_by
	 * @param string $sort
	 * @param bool $withPermissions
	 * @return mixed
	 */
	public function getAllRoles($order_by = 'name', $sort = 'asc', $withPermissions = false) {
		return Role::orderBy($order_by, $sort)->get();
	}

	/**
	 * @param $input
	 * @param $permissions
	 * @return bool
	 * @throws GeneralException
	 */
	public function create($input, $permissions) {
		if (Role::where('name', '=', $input['name'])->where('level', '=', $input['level'])->first())
			throw new GeneralException('That role already exists. Please choose a different name.');

		
		$role = new Role;
		$role->name = $input['name'];
                $role->level = $input['level'];

		if ($role->save()) {
			//Attach permissions
			return true;
		}

		throw new GeneralException("There was a problem creating this role. Please try again.");
	}

	/**
	 * @param $id
	 * @param $input
	 * @param $permissions
	 * @return bool
	 * @throws GeneralException
	 */
	public function update($id, $input, $permissions) {
		$role = $this->findOrThrowException($id);

		//Validate
		if (strlen($input['name']) == 0)
			throw new GeneralException('You must specify the role name.');

		$role->name = $input['name'];
                $role->level = $input['level'];

		if ($role->save()) {			
			return true;
		}

		throw new GeneralException('There was a problem updating this role. Please try again.');
	}

	/**
	 * @param $id
	 * @return bool
	 * @throws GeneralException
	 */
	public function destroy($id) {
		//Would be stupid to delete the administrator role
		if ($id == 1) //id is 1 because of the seeder
			throw new GeneralException("You can not delete the Administrator role.");

		$role = $this->findOrThrowException($id, true);

		//Don't delete the role is there are users associated
		if ($role->participants()->count() > 0)
			throw new GeneralException("You can not delete a role with associated users.");

		if ($role->delete())
			return true;

		throw new GeneralException("There was a problem deleting this role. Please try again.");
	}

	/**
	 * @return mixed
	 */
	public function getDefaultParticipantRole() {
		return Role::where('name', config('aio.participant.default_role'))->first();
	}
}