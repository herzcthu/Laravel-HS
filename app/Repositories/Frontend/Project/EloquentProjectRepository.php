<?php namespace App\Repositories\Frontend\Project;

use App\Exceptions\GeneralException;
use App\Project;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Input;

/**
 * Class EloquentProjectRepository
 * @package App\Repositories\Project
 */
class EloquentProjectRepository implements ProjectContract {


	/**
	 * @param OrganizationRepositoryContract $organization
	 * @param AuthenticationContract $auth
	 */
	public function __construct() {
	}

	/**
	 * @param $id
	 * @param bool $withOrganizations
	 * @return mixed
	 * @throws GeneralException
	 */
	public function findOrThrowException($id, $withOrganization = false, $withQuestions = false) {
		if($withOrganization && $withQuestions)
                        $project = Project::with('organization', 'questions')->find($id);
                elseif ($withQuestions)
                        $project = Project::with('questions')->find($id);
                elseif ($withOrganization)
			$project = Project::with('organization')->find($id);
		else
			$project = Project::find($id);

		if (! is_null($project)) return $project;

		throw new GeneralException('That project does not exist.');
	}

	/**
	 * @param $per_page
	 * @param string $order_by
	 * @param string $sort
	 * @param int $status
	 * @return mixed
	 */
	public function getProjectsPaginated($per_page, $order_by = 'id', $sort = 'asc') {
                $order_by = ((null !== Input::get('field'))? Input::get('field'):$order_by);
                $sort = ((null !== Input::get('sort'))? Input::get('sort'):$sort);
		if(!access()->user()->can('manage_organization')){
                    return Project::where('org_id', access()->user()->organization->id)->orderBy($order_by, $sort)->paginate($per_page);
                }else{
                    return Project::orderBy($order_by, $sort)->paginate($per_page);
                }
	}
        
        /**
	 * @param $per_page
	 * @param string $order_by
	 * @param string $sort
	 * @param int $status
	 * @return mixed
	 */
	public function searchProjects($queue, $status = 1, $order_by = 'id', $sort = 'asc') {
            $order_by = ((null !== Input::get('field'))? Input::get('field'):$order_by);
            $sort = ((null !== Input::get('sort'))? Input::get('sort'):$sort);
            return Project::where('status', $status)->orderBy($order_by, $sort)->search($queue)->get();
	}

	/**
	 * @param $per_page
	 * @return Paginator
	 */
	public function getDeletedProjectsPaginated($per_page) {
		return Project::onlyTrashed()->paginate($per_page);
	}

	/**
	 * @param string $order_by
	 * @param string $sort
	 * @return mixed
	 */
	public function getAllProjects($order_by = 'id', $sort = 'asc') {
		return Project::orderBy($order_by, $sort)->get();
	}
}