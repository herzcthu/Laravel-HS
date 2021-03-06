<?php namespace App\Repositories\Backend\Result;

use App\Exceptions\Backend\Access\Result\ResultNeedsOrganizationsException;
use App\Exceptions\GeneralException;
use App\Repositories\Backend\Organization\OrganizationContract;
use App\Repositories\Backend\Participant\ParticipantContract;
use App\Repositories\Backend\PLocation\PLocationContract;
use App\Repositories\Backend\Project\ProjectContract;
use App\Repositories\Backend\Result\ResultContract;
use App\Repositories\Backend\User\UserContract;
use App\Repositories\Frontend\Auth\AuthenticationContract;
use App\Result;
use App\Status;
use Illuminate\Pagination\Paginator;

/**
 * Class EloquentResultRepository
 * @package App\Repositories\Result
 */
class EloquentResultRepository implements ResultContract {

        protected $project;
        /**
	 * @var OrganizationContract
	 */
	protected $organization;
        
        protected $pcode;
        
        protected $participant;

        /**
	 * @var AuthenticationContract
	 */
	protected $user;
        
        protected $status;
        
        protected $questions;

        /**
	 * @param OrganizationRepositoryContract $organization
	 * @param AuthenticationContract $auth
	 */
	public function __construct(
                ProjectContract $project, 
                OrganizationContract $organization,
                PLocationContract $pcode,
                ParticipantContract $participant,
                UserContract $user,
                Status $status,
                \App\Repositories\Backend\Question\QuestionContract $questions) {
		$this->project = $project;
                $this->organization = $organization;
                $this->pcode = $pcode;
                $this->participant = $participant;
		$this->user = $user;
                $this->status = $status;
                $this->questions = $questions;
	}

	/**
	 * @param $id
	 * @param bool $withOrganizations
	 * @return mixed
	 * @throws GeneralException
	 */
	public function findOrThrowException($id, $withProject = false) {
		if ($withProject)
			$result = Result::with('project')->find($id);
		else
			$result = Result::find($id);

		if (! is_null($result)) return $result;

		throw new GeneralException('That result does not exist.');
	}
        
        public function getResultByQnum($qnum){
            $result_ByNum = Result::where('qnum', $qnum)->first();
            $result = $this->findOrThrowException($result_ByNum->id);
            return $result;
        }

	/**
	 * @param $per_page
	 * @param string $order_by
	 * @param string $sort
	 * @param int $status
	 * @return mixed
	 */
	public function getResultsPaginated($per_page, $order_by = 'id', $sort = 'asc') {
                $order_by = ((null !== Input::get('field'))? Input::get('field'):$order_by);
                $sort = ((null !== Input::get('sort'))? Input::get('sort'):$sort);
		return Result::orderBy($order_by, $sort)->paginate($per_page);
	}
        
        /**
	 * @param $per_page
	 * @param string $order_by
	 * @param string $sort
	 * @param int $status
	 * @return mixed
	 */
	public function searchResults($queue, $status = 1, $order_by = 'id', $sort = 'asc') {
            $order_by = ((null !== Input::get('field'))? Input::get('field'):$order_by);
            $sort = ((null !== Input::get('sort'))? Input::get('sort'):$sort);
            return Result::where('status', $status)->orderBy($order_by, $sort)->search($queue)->get();
	}

	/**
	 * @param $per_page
	 * @return Paginator
	 */
	public function getDeletedResultsPaginated($per_page) {
		return Result::onlyTrashed()->paginate($per_page);
	}

	/**
	 * @param string $order_by
	 * @param string $sort
	 * @return mixed
	 */
	public function getAllResults($project) {
		return Result::where('project_id', $project)->get();
	}

	/**
	 * @param $input
	 * @param $organizations
	 * @param $permissions
	 * @return bool
	 * @throws GeneralException
	 * @throws ResultNeedsOrganizationsException
	 */
	public function create($input, $project, $section) { 
            
                
                $validate = $input['validator_id'];
                if($project->validate == 'person'){
                    $resultable = $this->participant->getParticipantByCode($validate, $project->organization->id);
                    $result = Result::firstOrNew(['section_id' => $section, 'project_id' => $project->id,'resultable_id' => $resultable->id, 'resultable_type' => 'App\Participant']);
                    //$pcode = $person->pcode;
                }else{
                    $resultable = $this->pcode->findOrThrowException($validate); //dd($resultable->primaryid);
                    $result = Result::firstOrNew(['section_id' => $section, 'project_id' => $project->id,'resultable_id' => $resultable->primaryid, 'resultable_type' => 'App\PLocation']);
                    //$person = $pcode->participants->first();
                    $result->resultable_id = $resultable->primaryid;
                }
                
                $result->results = $input['answer'];
                $result->section_id = $section;
                $result->information = $this->updateStatus($project, $section, $input['answer']);
                $current_user = auth()->user();
                $result->user()->associate($current_user);
                
                $result->project()->associate($project);
                if(isset($resultable)){
                    $result->resultable()->associate($resultable);
                }
                
                
                
                if ($result->save()) {
                    \App\Answers::where('status_id', $result->id)->delete();
                   foreach($input['answer'] as $qid => $answers){
                        $q = $this->questions->getQuestionByQnum($qnum, $project->id);
                        
                        foreach($answers as $akey => $aval){
                            if($akey == 'radio'){
                                $answerkey = $aval;
                            }else{
                                $answerkey = $akey;
                            }
                            $qanswer = $q->qanswers->where('akey', $answerkey)->first();
                            if(in_array($qanswer->type,['radio','checkbox'])){
                                $answerVal = $qanswer->value;
                            }else{
                                $answerVal = $aval;
                            }
                            $answerR = \App\Answers::firstOrNew(['qid' => $q->id, 'akey' => $answerkey, 'status_id' => $result->id]);
                            if(!empty($answerVal)){
                                $answerR->value = $answerVal;
                                $result->answers()->save($answerR);
                            }
                        }


                    }
			return true;
		}

		throw new GeneralException('There was a problem creating this result. Please try again.');
	}

	/**
	 * @param $id
	 * @param $input
	 * @param $organizations
	 * @return bool
	 * @throws GeneralException
	 */
	public function update($id, $input, $project) {
		$result = $this->findOrThrowException($id, true); //print_r($input); print_r($result);die();
		$related_id = $input['related_data']['q'];
                if(!empty($related_id)){
                    $this->flushParent($related_id, $result);
                }
                $result->section = $input['section'];
                if($input['report'] == 'none' || empty($input['report'])) {
                    $result->report = null;
                } else {
                    $result->report = $input['report'];
                }
                $result->answers = $input['answers'];
                $result->related_data = $input['related_data'];
                $result->answer_view = $input['answer_view'];
                $result->sameanswer = isset($input['sameanswer']) ? 1 : 0;
                $display['qnum'] = isset($input['display']['qnum'])? 1 : 0;
                $display['result'] = isset($input['display']['result'])? 1 : 0;
                $result->display = $display;
                //$result->save();
                //$toUpdate = $result->whereQnum($input['qnum'])->whereProjectId($project['project_id'])->first();
                //dd($toUpdate);
		if ($result->whereQnum($input['qnum'])->whereProjectId($project['project_id'])->first()->update($input)) { 
                    \App\Answers::where('status_id', $result->id)->delete();
                    foreach($input['answer'] as $qnum => $answers){
                        $q = $this->questions->getQuestionByQnum($qnum, $project->id);
                        
                        foreach($answers as $akey => $aval){
                            if($akey == 'radio'){
                                $answerkey = $aval;
                            }else{
                                $answerkey = $akey;
                            }
                            $qanswer = $q->qanswers->where('akey', $answerkey)->first();
                            if(in_array($qanswer->type,['radio','checkbox'])){
                                $answerVal = $qanswer->value;
                            }else{
                                $answerVal = $aval;
                            }
                            $answerR = \App\Answers::firstOrNew(['qid' => $q->id, 'akey' => $answerkey, 'status_id' => $result->id]);
                            if(!empty($answerVal)){
                                $answerR->value = $answerVal;
                                $result->answers()->save($answerR);
                            }
                        }


                    }
                        
                        
			return true;
		}

		throw new GeneralException('There was a problem updating this result. Please try again.');
	}

	
	/**
	 * @param $id
	 * @return bool
	 * @throws GeneralException
	 */
	public function destroy($id) {
		if (auth()->id() == $id)
			throw new GeneralException("You can not delete yourself.");

		$result = $this->findOrThrowException($id);
		if ($result->delete())
			return true;

		throw new GeneralException("There was a problem deleting this result. Please try again.");
	}

	/**
	 * @param $id
	 * @return boolean|null
	 * @throws GeneralException
	 */
	public function delete($id) {
		$result = $this->findOrThrowException($id, true);

		//Detach all organizations & permissions
		$result->detachOrganizations($result->organizations);
		$result->detachPermissions($result->permissions);

		try {
			$result->forceDelete();
		} catch (Exception $e) {
			throw new GeneralException($e->getMessage());
		}
	}

	/**
	 * @param $id
	 * @return bool
	 * @throws GeneralException
	 */
	public function restore($id) {
		$result = $this->findOrThrowException($id);

		if ($result->restore())
			return true;

		throw new GeneralException("There was a problem restoring this result. Please try again.");
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

		$result = $this->findOrThrowException($id);
		$result->status = $status;

		if ($result->save())
			return true;

		throw new GeneralException("There was a problem updating this result. Please try again.");
	}

	/**
	 * Check to make sure at lease one organization is being applied or deactivate result
	 * @param $result
	 * @param $organizations
	 * @throws ResultNeedsOrganizationsException
	 */
	private function validateOrganizationAmount($result, $organizations) {
		//Validate that there's at least one organization chosen, placing this here so
		//at lease the result can be updated first, if this fails the organizations will be
		//kept the same as before the result was updated
		if (count($organizations) == 0) {
			//Deactivate result
			$result->status = 0;
			$result->save();

			$exception = new ResultNeedsOrganizationsException();
			$exception->setValidationErrors('You must choose at lease one organization. Result has been created but deactivated.');

			//Grab the result id in the controller
			$exception->setResultID($result->id);
			throw $exception;
		}
	}

	/**
	 * @param $input
	 * @param $result
	 * @throws GeneralException
	 */
	private function checkResultByEmail($input, $result)
	{
		//Figure out if email is not the same
		if ($result->email != $input['email'])
		{
			//Check to see if email exists
			if (Result::where('email', '=', $input['email'])->first())
				throw new GeneralException('That email address belongs to a different result.');
		}
	}

	/**
	 * @param $organizations
	 * @param $result
	 */
	private function flushParent($parent_id, $result)
	{
		$result->parent()->dissociate();
                $parent = $this->findOrThrowException($parent_id);
                $result->parent()->associate($parent);
	}

	private function flushProject($project_id, $result)
	{
		$result->project()->dissociate();
                $project = $this->project->findOrThrowException($project_id);
                $result->project()->associate($project);
                //$result->save();
	}

	/**
	 * @param $organizations
	 * @throws GeneralException
	 */
	private function checkResultOrganizationsCount($organizations)
	{
		//Result Updated, Update Organizations
		//Validate that there's at least one organization chosen
		if (count($organizations['assignees_organizations']) == 0)
			throw new GeneralException('You must choose at least one organization.');
	}

	/**
	 * @param $input
	 * @return mixed
	 */
	private function createResultStub($input)
	{
		$result = new Result;
                $result->section_id = isset($input['section'])?$input['section']:null;
                //$result->report = isset($input['report'])?$input['report']:null;
                $result->results = $input['answer'];
		return $result;
	}

    private function updateStatus($project, $section, $answers) {
            $section = (int) $section;
            //dd($answers);
            $section_qcount = $project->questions->where('section', $section)->count(); 
            
            $formula = $project->sections[$section]->formula; dd($formula);
            
            $anscount = count(array_filter(array_values(array_unique(array_dot($answers)))));
            
            if((count(array_values(array_unique(array_dot($answers)))) == 1 && array_values(array_unique(array_dot($answers)))[0] == "" ) || $anscount == 0 ){//dd(true);
                $status = 'missing';
            }elseif ($anscount != $section_qcount) {
                $status = 'incomplete';
            }elseif($anscount == $section_qcount){
                
                if(!empty($formulas)){
                    $formula = explode(',', $formulas);//dd($answers[]);
                    foreach ($formula as $logic){
                        preg_match('/(.*)([=<>])(.*)/', $logic, $variables);
                        $left = $variables[1];
                        $bitwise = $variables[2];
                        $right = $variables[3];
                        
                        $mathsign = preg_quote('+-/*');
                        if($bitwise == '='){
                           if($this->logic($left, $var) != $this->logic($right, $var)){
                               return 'error';
                           } 
                        }
                        if($bitwise == '>'){
                           if($this->logic($left, $var) < $this->logic($right, $var)){
                               return 'error';
                           }
                        }
                        if($bitwise == '<'){
                            if($this->logic($left, $var) > $this->logic($right, $var)){
                               return 'error';
                           }
                        }
                    }
                }else{
                    return 'complete';
                }
            }else{
                
            }
            
           return $status;
    }
    
    private function logic($formula, $variables) {
        //dd($variables);
        $compiler = new Compiler();//dd($formula);
        try{
        $executable = $compiler->compile($formula);
        $result = $executable->run($variables);
        }catch(ParserException $e){
            throw new GeneralException('Logical check formula error!');
        }
        return $result;
    }
    
    private function updateResults($project, $result, $section, $answers) {
        try{
            $results = $result->results;
            foreach($results as $sec => $rs){
                foreach ($rs as $rk => $res){
                    $ans[$sec][$rk] = $res;
                }
                
            }
        } catch (\ErrorException $ex) {

        }
        $status = $this->updateStatus($project, $section, $answers);
        $ans[$section]['results'] = $answers;
        $ans[$section]['status'] = $status;
        //dd($ans);
        $result->results = $ans;
        //dd($result);
        return $result;
    }

}