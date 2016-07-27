<?php namespace App\Http\Controllers\Backend\Participant;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Participant\Role\RoleRepositoryContract;
use App\Http\Requests\Backend\Participant\Role\CreateRoleRequest;
use App\Http\Requests\Backend\Participant\Role\UpdateRoleRequest;

/**
 * Class RoleController
 * @package App\Http\Controllers\Participant
 */
class ProleController extends Controller {

	/**
	 * @var RoleRepositoryContract
	 */
	protected $roles;

	/**
	 * @param RoleRepositoryContract $roles
	 * @param PermissionRepositoryContract $permissions
	 */
	public function __construct(
		RoleRepositoryContract $roles) {
		$this->roles = $roles;		
	}

	/**
	 * @return mixed
	 */
	public function index() {
		return view('backend.participant.roles.index')
			->withRoles($this->roles->getRolesPaginated(50));
	}

	/**
	 * @return mixed
	 */
	public function create() {
		return view('backend.participant.roles.create');
	}

	/**
	 * @param CreateRoleRequest $request
	 * @return mixed
	 */
	public function store(CreateRoleRequest $request) {
		$this->roles->create($request->except('role_permissions'), $request->only('role_permissions'));
		return redirect()->route('admin.participants.proles.index')->withFlashSuccess('The role was successfully created.');
	}

	/**
	 * @param $id
	 * @return mixed
	 */
	public function edit($id) {
		$role = $this->roles->findOrThrowException($id, true);
		return view('backend.participant.roles.edit')
			->withRole($role);
	}

	/**
	 * @param $id
	 * @param UpdateRoleRequest $request
	 * @return mixed
	 */
	public function update($id, UpdateRoleRequest $request) {
		$this->roles->update($id, $request->except('role_permissions'), $request->only('role_permissions'));
		return redirect()->route('admin.participants.proles.index')->withFlashSuccess('The role was successfully updated.');
	}

	/**
	 * @param $id
	 * @return mixed
	 */
	public function destroy($id) {
		$this->roles->destroy($id);
		return redirect()->route('admin.participants.proles.index')->withFlashSuccess('The role was successfully deleted.');
	}
}