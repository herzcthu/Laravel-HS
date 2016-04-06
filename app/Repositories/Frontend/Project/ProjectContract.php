<?php namespace App\Repositories\Frontend\Project;

/**
 * Interface ProjectContract
 * @package App\Repositories\Project
 */
interface ProjectContract {

	/**
	 * @param $id
	 * @param bool $withRoles
	 * @return mixed
	 */
	public function findOrThrowException($id, $withOrganization = false, $withQuestions = false);

	/**
	 * @param $per_page
	 * @param string $order_by
	 * @param string $sort
	 * @param $status
	 * @return mixed
	 */
	public function getProjectsPaginated($per_page, $order_by = 'id', $sort = 'asc');
        
        /**
	 * @param $per_page
	 * @param string $order_by
	 * @param string $sort
	 * @param $status
	 * @return mixed
	 */
	public function searchProjects($queue, $status = 1, $order_by = 'id', $sort = 'asc');

	/**
	 * @param $per_page
	 * @return \Illuminate\Pagination\Paginator
	 */
	public function getDeletedProjectsPaginated($per_page);

	/**
	 * @param string $order_by
	 * @param string $sort
	 * @return mixed
	 */
	public function getAllProjects($order_by = 'id', $sort = 'asc');

	

	
}