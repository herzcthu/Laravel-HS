<?php namespace App\Repositories\Frontend\Question;

/**
 * Interface QuestionContract
 * @package App\Repositories\Question
 */
interface QuestionContract {

	/**
	 * @param $id
	 * @param bool $withRoles
	 * @return mixed
	 */
	public function findOrThrowException($id, $withProject = false);
        
        public function getQuestionByQnum($qnum, $section, $project_id);

	/**
	 * @param $per_page
	 * @param string $order_by
	 * @param string $sort
	 * @param $status
	 * @return mixed
	 */
	public function getQuestionsPaginated($per_page, $order_by = 'id', $sort = 'asc');
        
        /**
	 * @param $per_page
	 * @param string $order_by
	 * @param string $sort
	 * @param $status
	 * @return mixed
	 */
	public function searchQuestions($queue, $status = 1, $order_by = 'id', $sort = 'asc');

	/**
	 * @param $per_page
	 * @return \Illuminate\Pagination\Paginator
	 */
	public function getDeletedQuestionsPaginated($per_page);

	/**
	 * @param string $order_by
	 * @param string $sort
	 * @return mixed
	 */
	public function getAllQuestions($order_by = 'id', $sort = 'asc');

	/**
	 * @param $input
	 * @param $roles
	 * @return mixed
	 */
	public function create($input, $project);

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