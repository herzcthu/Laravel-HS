<?php namespace App\Repositories\Frontend\Result;

/**
 * Interface ResultContract
 * @package App\Repositories\Result
 */
interface ResultContract {

	/**
	 * @param $id
	 * @param bool $withRoles
	 * @return mixed
	 */
	public function findOrThrowException($id, $withProject = false);
        
        public function getResultBySection($section, $project, $resultable, $qnum, $anskey, $incident = '');

	/**
	 * @param $per_page
	 * @param string $order_by
	 * @param string $sort
	 * @param $status
	 * @return mixed
	 */
	public function getResultsPaginated($per_page, $order_by = 'id', $sort = 'asc');
        
        /**
	 * @param $per_page
	 * @param string $order_by
	 * @param string $sort
	 * @param $status
	 * @return mixed
	 */
	public function searchResults($queue, $status = 1, $order_by = 'id', $sort = 'asc');

	/**
	 * @param $per_page
	 * @return \Illuminate\Pagination\Paginator
	 */
	public function getDeletedResultsPaginated($per_page);

	/**
	 * @param string $order_by
	 * @param string $sort
	 * @return mixed
	 */
	public function getAllResults($order_by = 'id', $sort = 'asc');

	/**
	 * @param $input
	 * @param $roles
	 * @return mixed
	 */
	public function create($input, $project, $section);

	/**
	 * @param $id
	 * @param $input
	 * @param $roles
	 * @return mixed
	 */
	public function update($id, $input, $project_id);

	/**
	 * @param $id
	 * @return mixed
	 */
	public function destroy($id);

	/**
	 * @param $id
	 * @return mixed
	 */
	public function delete($id);

	/**
	 * @param $id
	 * @return mixed
	 */
	public function restore($id);

	/**
	 * @param $id
	 * @param $status
	 * @return mixed
	 */
	public function mark($id, $status);

	
}