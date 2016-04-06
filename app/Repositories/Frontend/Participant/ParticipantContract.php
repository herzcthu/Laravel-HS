<?php namespace App\Repositories\Frontend\Participant;

/**
 * Interface ParticipantContract
 * @package App\Repositories\Participant
 */
interface ParticipantContract {

	/**
	 * @param $id
	 * @param bool $withRoles
	 * @return mixed
	 */
	public function findOrThrowException($id, $withRoles = false);

	/**
	 * @param $per_page
	 * @param string $order_by
	 * @param string $sort
	 * @param $status
	 * @return mixed
	 */
	public function getParticipantsPaginated($per_page, $status = 1, $order_by = 'id', $sort = 'asc');
        
        /**
	 * @param $per_page
	 * @param string $order_by
	 * @param string $sort
	 * @param $status
	 * @return mixed
	 */
	public function searchParticipants($queue, $status = 1, $order_by = 'id', $sort = 'asc');

	/**
	 * @param $per_page
	 * @return \Illuminate\Pagination\Paginator
	 */
	public function getDeletedParticipantsPaginated($per_page);
        
        public function getParticipantByCode($pcode, $org);

        /**
	 * @param string $order_by
	 * @param string $sort
	 * @return mixed
	 */
	public function getAllParticipants($order_by = 'id', $sort = 'asc');

}