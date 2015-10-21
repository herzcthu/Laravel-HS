<?php namespace App\Repositories\Frontend\Participant\Role;

/**
 * Interface RoleRepositoryContract
 * @package App\Repositories\Role
 */
interface RoleRepositoryContract {

	/**
	 * @param $id
	 * @param bool $withPermissions
	 * @return mixed
	 */
	public function findOrThrowException($id, $withPermissions = false);

        public function getRoleLevel($id);
	/**
	 * @param $per_page
	 * @param string $order_by
	 * @param string $sort
	 * @return mixed
	 */
	public function getRolesPaginated($per_page, $order_by = 'id', $sort = 'asc');

	/**
	 * @param string $order_by
	 * @param string $sort
	 * @param bool $withPermissions
	 * @return mixed
	 */
	public function getAllRoles($order_by = 'id', $sort = 'asc', $withPermissions = false);

	

	/**
	 * @return mixed
	 */
	public function getDefaultParticipantRole();
}