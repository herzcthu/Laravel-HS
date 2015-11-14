<?php namespace App\Repositories\Backend\Project;

use App\Exceptions\Backend\Access\Project\ProjectNeedsOrganizationsException;
use App\Exceptions\GeneralException;
use App\Media;
use App\PLocation;
use App\Project;
use App\QAnswers;
use App\Repositories\Backend\Organization\OrganizationContract;
use App\Repositories\Backend\Organization\OrganizationRepositoryContract;
use App\Repositories\Backend\PLocation\PLocationContract;
use App\Repositories\Frontend\Auth\AuthenticationContract;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Class EloquentProjectRepository
 * @package App\Repositories\Project
 */
class EloquentProjectRepository implements ProjectContract {

	/**
	 * @var OrganizationRepositoryContract
	 */
	protected $organization;

	/**
	 * @var AuthenticationContract
	 */
	protected $auth;
        
        protected $pcode;
        

        /**
	 * @param OrganizationRepositoryContract $organization
	 * @param AuthenticationContract $auth
	 */
	public function __construct(OrganizationContract $organization, AuthenticationContract $auth, PLocationContract $pcode) {
		$this->organization = $organization;
		$this->auth = $auth;
                $this->pcode = $pcode;
	}

	/**
	 * @param $id
	 * @param bool $withOrganizations
	 * @return mixed
	 * @throws GeneralException
	 */
	public function findOrThrowException($id, $withRelations = []) {
            
                if(!empty($withRelations)){
                    $relations = implode(',', $withRelations);
                    $project = Project::with($relations)->find($id);
                }else{
                    $project = Project::find($id);
                }

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
                    if(null !== access()->user()->organization){
                        return Project::where('org_id', access()->user()->organization->id)->orderBy($order_by, $sort)->paginate($per_page);
                    }else{
                        throw new GeneralException('User is not organization member!');
                    }
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
            if(!access()->user()->can('manage_organization')){
                return Project::where('org_id', access()->user()->organization->id)->orderBy($order_by, $sort)->get();
            }else{
                return Project::orderBy($order_by, $sort)->get();
            }
	}

	/**
	 * @param $input
	 * @param $organizations
	 * @param $permissions
	 * @return bool
	 * @throws GeneralException
	 * @throws ProjectNeedsOrganizationsException
	 */
	public function create($input, $org_id) {
                $project = $this->createProjectStub($input, $org_id['organization']);
                if(array_key_exists('project', $input)){
                $parent_project = $input['project'];
                
                    if(!empty($parent_project) && $parent_project != 'none'){
                           $parent = $this->findOrThrowException($parent_project);
                           
                           $project->parent()->associate($parent);
                           $organization_id = $parent->organization->id;
                    }else{
                        $organization_id = $org_id['organization'];
                    }
                }else{
                    $organization_id = $org_id['organization'];
                }
                
                $organization = $this->organization->findOrThrowException($organization_id);
		$project->organization()->associate($organization);
                if ($project->save()) {
			return true;
		}

		throw new GeneralException('There was a problem creating this project. Please try again.');
	}

	/**
	 * @param $id
	 * @param $input
	 * @param $organizations
	 * @return bool
	 * @throws GeneralException
	 */
	public function update($project, $input, $parent_project, $organization) {
		//$project = $this->findOrThrowException($id);
            //dd($organization);
                $project = $this->createProjectStub($input, $organization['organization']);
                $project->parent()->dissociate();
		if(!empty($parent_project) && $parent_project['project'] != 'none'){                    
                           $parent = $this->findOrThrowException($parent_project['project']);
                           
                           $project->parent()->associate($parent);
                           $org_id = $parent->organization->id;
                    }else{
                        $org_id = $organization['organization'];
                    }
                    $this->flushOrganization($org_id, $project);
		if ($project->save()) { 
                    return true;
		}

		throw new GeneralException('There was a problem updating this project. Please try again.');
	}

	

	/**
	 * @param $id
	 * @return bool
	 * @throws GeneralException
	 */
	public function destroy($id) {
		if (auth()->id() == $id)
			throw new GeneralException("You can not delete yourself.");

		$project = $this->findOrThrowException($id);
		if ($project->delete())
			return true;

		throw new GeneralException("There was a problem deleting this project. Please try again.");
	}

	/**
	 * @param $id
	 * @return boolean|null
	 * @throws GeneralException
	 */
	public function delete($project) {
		if(!$project->questions->isEmpty()){
                    throw new GeneralException("Project is not empty. You can not delete this project.");
                }
                
		//Disassociate organization
		$project->organization()->dissociate();

		try {
			$project->delete();
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
		$project = $this->findOrThrowException($id);

		if ($project->restore())
			return true;

		throw new GeneralException("There was a problem restoring this project. Please try again.");
	}
        
        public function export($project) {
            set_time_limit(0);
            if($project->type == 'checklist'){
                $locations = PLocation::where('org_id', $project->org_id)->with('results')->with('answers')->get();
                foreach($locations as $k => $location){
                    $array[$k]['state'] = $location->state;
                    $array[$k]['district'] = $location->district;
                    $array[$k]['township'] = $location->township;
                    $array[$k]['pcode'] = $location->pcode;
                    foreach ($project->sections as $sk => $section){
                        if(null !== $location->results->where('project_id', $project->id)->where('section_id', $sk)->first()){
                            $information = $location->results->where('project_id', $project->id)->where('section_id', $sk)->first()->information;
                            switch ($information) {
                                case 'complete':
                                    $array[$k][$section->text] = 1;
                                    break;
                                case 'incomplete':
                                    $array[$k][$section->text] = 2;
                                    break;
                                case 'error':
                                    $array[$k][$section->text] = 3;
                                    break;
                                default:
                                    $array[$k][$section->text] = "0";
                                    break;
                            }

                        }else{
                            $array[$k][$section->text] = "0";
                        }
                        //$array[$k][$section->text] = (null !== $location->results->where('project_id', $project->id)->where('section_id', $sk)->first())? $location->results->where('project_id', $project->id)->where('section_id', $sk)->first()->information:'missing';
                        $array[$k][$section->text.' Time'] = (null !== $location->results->where('project_id', $project->id)->where('section_id', $sk)->first())? $location->results->where('project_id', $project->id)->where('section_id', $sk)->first()->updated_at:'';
                        $array[$k][$section->text.' Data Clerk'] = (null !== $location->results->where('project_id', $project->id)->where('section_id', $sk)->first())? $location->results->where('project_id', $project->id)->where('section_id', $sk)->first()->user->name:'';
                    }
                    foreach ($project->questions as $question){
                        $radios = QAnswers::where('qid', $question->id)->where('type', 'radio')->get();
                        $text = QAnswers::where('qid', $question->id)->whereIn('type', ['text','textarea','number','time'])->get();
                        $checkbox = QAnswers::where('qid', $question->id)->where('type', 'checkbox')->get();
                        //dd($location->answers->where('qid', $question->id)->first());
                        if(!$radios->isEmpty()){
                            $array[$k][$question->qnum] = (null !== $location->answers->where('qid', $question->id)->first())? $location->answers->where('qid', $question->id)->first()->value:'';
                        }
                        if(!$text->isEmpty()){
                            foreach($text as $t){
                                $array[$k][$t->akey] = (null !== $location->answers->where('qid', $question->id)->where('akey', $t->akey)->first())? $location->answers->where('qid', $question->id)->where('akey', $t->akey)->first()->value:'';
                            }
                        }
                        if(!$checkbox->isEmpty()){
                            foreach($checkbox as $c){
                                $array[$k][$c->akey] = (null !== $location->answers->where('qid', $question->id)->where('akey', $c->akey)->first())? $location->answers->where('qid', $question->id)->where('akey', $c->akey)->first()->value:'';
                            }
                        }
                    }
                }
            }
            if($project->type == 'incident'){
                $results = \App\Result::where('project_id', $project->id)->get();
                foreach($results as $ik => $incident){
                    if($project->validate == 'pcode'){
                    $array[$ik]['pcode'] = $incident->resultable->pcode;
                    $array[$ik]['state'] = $incident->resultable->state;
                    $array[$ik]['township'] = $incident->resultable->township;
                    $array[$ik]['village_tract'] = $incident->resultable->village_tract;
                    $array[$ik]['village'] = $incident->resultable->village;
                    $array[$ik]['incident'] = $incident->incident_id;
                    foreach ($project->sections as $sk => $section){
                        if(null !== $results->where('project_id', $project->id)->where('section_id', $sk)->first()){
                            $information = $results->where('project_id', $project->id)->where('section_id', $sk)->first()->information;
                            switch ($information) {
                                case 'complete':
                                    $array[$ik][$section->text] = 1;
                                    break;
                                case 'incomplete':
                                    $array[$ik][$section->text] = 2;
                                    break;
                                case 'error':
                                    $array[$ik][$section->text] = 3;
                                    break;
                                default:
                                    $array[$ik][$section->text] = "0";
                                    break;
                            }

                        }else{
                            $array[$ik][$section->text] = "0";
                        }
                        //$array[$k][$section->text] = (null !== $location->results->where('project_id', $project->id)->where('section_id', $sk)->first())? $location->results->where('project_id', $project->id)->where('section_id', $sk)->first()->information:'missing';
                        $array[$ik][$section->text.' Time'] = (null !== $results->where('project_id', $project->id)->where('section_id', $sk)->first())? $results->where('project_id', $project->id)->where('section_id', $sk)->first()->updated_at:'';
                        $array[$ik][$section->text.' Data Clerk'] = (null !== $results->where('project_id', $project->id)->where('section_id', $sk)->first())? $results->where('project_id', $project->id)->where('section_id', $sk)->first()->user->name:'';
                    }
                    
                    foreach ($project->questions as $question){
                        $radios = QAnswers::where('qid', $question->id)->where('type', 'radio')->get();
                        $text = QAnswers::where('qid', $question->id)->whereIn('type', ['text','textarea','number','time'])->get();
                        $checkbox = QAnswers::where('qid', $question->id)->where('type', 'checkbox')->get();
                        //dd($incident->answers->where('qid', $question->id)->first());
                        if(!$radios->isEmpty()){
                            $array[$ik][$question->qnum] = (null !== $incident->answers->where('qid', $question->id)->first())? $incident->answers->where('qid', $question->id)->first()->value:'';
                        }
                        if(!$text->isEmpty()){
                            foreach($text as $t){
                                $array[$ik][$t->akey] = (null !== $incident->answers->where('qid', $question->id)->where('akey', $t->akey)->first())? $incident->answers->where('qid', $question->id)->where('akey', $t->akey)->first()->value:'';
                            }
                        }
                        if(!$checkbox->isEmpty()){
                            foreach($checkbox as $c){
                                $array[$ik][$c->akey] = (null !== $incident->answers->where('qid', $question->id)->where('akey', $c->akey)->first())? $incident->answers->where('qid', $question->id)->where('akey', $c->akey)->first()->value:'';
                            }
                        }
                    }
                    }else{
                        /**
                         * To Do: need to add export function when using participant validation code
                         */
                        throw new GeneralException('Not yet implemented.');
                    }
                }
            }
            $filename = preg_filter('/[^\d\w\s\.]/', ' ', $project->name.Carbon::now());
            $file = Excel::create(str_slug($filename), function($excel) use($array) {

                $excel->sheet('Sheetname', function($sheet) use($array) {

                    $sheet->fromArray($array);

                });

            });
            $store =  $file->store('xls', false, true);// dd($store);
            $media = Media::firstOrNew(['filename' => $store['title'], 'filedir' => $store['full']]);
            $media->filename = $store['title'];
            $media->filedir = $store['full'];
            $media->file = json_encode($store);
            $media->status = 1;
            $current_user = auth()->user();
            $media->owner()->associate($current_user);
            $media2 = Media::firstOrNew(['filename' => $storecsv['title'], 'filedir' => $storecsv['full']]);
            $media2->filename = $storecsv['title'];
            $media2->filedir = $storecsv['full'];
            $media2->file = json_encode($storecsv);
            $media2->status = 1;
            $media2->owner()->associate($current_user);
            $media2->save();
            if($media->save()){
                //return $file->download('xls');
                return true;
            }
            throw new GeneralException('There was a problem creating export data. Please try again.');
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

		$project = $this->findOrThrowException($id);
		$project->status = $status;

		if ($project->save())
			return true;

		throw new GeneralException("There was a problem updating this project. Please try again.");
	}

	/**
	 * Check to make sure at lease one organization is being applied or deactivate project
	 * @param $project
	 * @param $organizations
	 * @throws ProjectNeedsOrganizationsException
	 */
	private function validateOrganizationAmount($project, $organizations) {
		//Validate that there's at least one organization chosen, placing this here so
		//at lease the project can be updated first, if this fails the organizations will be
		//kept the same as before the project was updated
		if (count($organizations) == 0) {
			//Deactivate project
			$project->status = 0;
			$project->save();

			$exception = new ProjectNeedsOrganizationsException();
			$exception->setValidationErrors('You must choose at lease one organization. Project has been created but deactivated.');

			//Grab the project id in the controller
			$exception->setProjectID($project->id);
			throw $exception;
		}
	}


	/**
	 * @param $organizations
	 * @param $project
	 */
	private function flushOrganization($org_id, $project)
	{
		$project->organization()->dissociate();
                $organization = $this->organization->findOrThrowException($org_id);
                $project->organization()->associate($organization);
	}

	

	/**
	 * @param $input
	 * @return mixed
	 */
	private function createProjectStub($input, $org_id)
	{
		$project = Project::firstOrNew(['name' => $input['name'], 'org_id' => $org_id]);
		$project->name = $input['name'];
                $project->desc = $input['desc'];
                $project->type = $input['type'];
                $project->validate = $input['validate'];
                $project->sections = $input['sections'];
                $project->reporting = $input['reporting'];
		
		return $project;
	}
}