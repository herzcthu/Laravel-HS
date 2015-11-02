<?php namespace App\Repositories\Frontend\Result;

use App\Answers;
use App\Exceptions\GeneralException;
use App\Repositories\Frontend\Auth\AuthenticationContract;
use App\Repositories\Frontend\Organization\OrganizationContract;
use App\Repositories\Frontend\Participant\ParticipantContract;
use App\Repositories\Frontend\PLocation\PLocationContract;
use App\Repositories\Frontend\Project\ProjectContract;
use App\Repositories\Frontend\Question\QuestionContract;
use App\Repositories\Frontend\Result\ResultContract;
use App\Result;
use FormulaInterpreter\Compiler;
use FormulaInterpreter\Parser\ParserException;
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
        
        protected $participant;
        
        protected $pcode;
        
        protected $questions;

        /**
	 * @param OrganizationRepositoryContract $organization
	 * @param AuthenticationContract $auth
	 */
	public function __construct(ProjectContract $project, 
                                    OrganizationContract $organization, 
                                    PLocationContract $pcode,
                                    ParticipantContract $participant,
                                    QuestionContract $questions) {
		$this->project = $project;
                $this->organization = $organization;
                $this->participant = $participant;
                $this->pcode = $pcode;
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
        
        /**
         * 
         * @param integer $section (Section in form)
         * @param object $project (Project object)
         * @param integer/string $resultable (resultable either location or participant)
         * @param string $qnum (Question Number)
         * @param string $anskey (Answer Key)
         * @param integer $incident (incident number)
         * @return integer/string
         */
        public function getResultBySection($section, $project, $resultable, $qnum, $anskey, $incident = ''){
            $result_ByNum = Result::where('project_id', $project)->where('section_id', $section)->where('resultable_id', $resultable);
            if(!empty($incident)){      //dd($incident);
                  $result =  $result_ByNum->where('incident_id', (int) $incident)->first();
            }else{
                $result = $result_ByNum->first();
            }
            if (! is_null($result)){
                if(!is_null($result->answers)){
                    foreach($result->answers as $ans){
                        if($ans->akey == $anskey){
                            return $ans->value;
                        }
                    }
                    /**
                    if(property_exists($result_ByNum->results, $qnum)){
                        if(property_exists($result_ByNum->results->{$qnum}, $anskey)){
                        return $result_ByNum->results->{$qnum}->{$anskey};
                        }
                    }
                    if(array_key_exists($qnum, $result_ByNum->results)){
                        if(array_key_exists($anskey, $result_ByNum->results[$qnum])){
                            return $result_ByNum->results[$qnum][$anskey];
                        }
                    }
                    
                     * 
                     */
                }
             
            }
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
	public function getAllResults($order_by = 'id', $sort = 'asc') {
		return Result::orderBy($order_by, $sort)->get();
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
                if($project->type == 'incident'){
                    $incident = \App\Result::where('project_id', $project->id)->where('section_id', $section)->orderBy('incident_id', 'desc')->first();
                    if(!is_null($incident)){
                    $incident_id = $incident->incident_id + 1;
                    }else{
                        $incident_id = 1;
                    }
                    
                }else{
                    $incident_id = null;
                }
                if($project->validate == 'person'){
                    $resultable = $this->participant->getParticipantByCode($validate, $project->organization->id);
                    $result = Result::firstOrNew(['section_id' => $section, 'project_id' => $project->id, 'incident_id' => $incident_id,'resultable_id' => $resultable->id, 'resultable_type' => 'App\Participant']);
                    //$pcode = $person->pcode;
                }else{
                    $validator = $validate.'-'.$project->organization->id;
                    try{
                        $resultable2 = $this->pcode->findOrThrowException($validator);
                        
                    }catch(GeneralException $e){
                        unset($e);
                    }
                    try{
                        $resultable1 = $this->pcode->findOrThrowException($validate);
                        
                    }catch(GeneralException $e){
                        unset($e);
                    }
                    if(isset($resultable1)){
                        $resultable = $resultable1;
                    }
                    if(isset($resultable2)){
                        $resultable = $resultable2;
                    }
                    $result = Result::firstOrNew(['section_id' => $section, 'project_id' => $project->id, 'incident_id' => $incident_id,'resultable_id' => $resultable->primaryid, 'resultable_type' => 'App\PLocation']);
                      
                    //$person = $pcode->participants->first();
                    $result->resultable_id = $resultable->primaryid;
                }
                if($incident_id){
                    $result->incident_id = $incident_id;
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
                    Answers::where('status_id', $result->id)->delete();
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
                            
                            $answerR = Answers::firstOrNew(['qid' => $q->id, 'akey' => $answerkey, 'status_id' => $result->id]);
                            if(isset($answerVal)){
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
	public function update($id, $input, $project) {dd($input);
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
                if(isset($input['incident_id'])){
                    $result->incident_id = $input['incident_id'];
                }
                $result->answers = $input['answers'];
                $result->related_data = $input['related_data'];
                $result->answer_view = $input['answer_view'];
                $result->sameanswer = isset($input['sameanswer']) ? 1 : 0;
                $display['qnum'] = isset($input['display']['qnum'])? 1 : 0;
                $display['result'] = isset($input['display']['result'])? 1 : 0;
                $result->display = $display;
                
		if ($result->whereQnum($input['qnum'])->whereProjectId($project['project_id'])->first()->update($input)) { 
                    Answers::where('status_id', $result->id)->delete();
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
                            $answerR = Answers::firstOrNew(['qid' => $q->id, 'akey' => $answerkey, 'status_id' => $result->id]);
                            dd($answerVal);
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
                if(isset($input['incident_id'])){
                    $result->incident_id = $input['incident_id'];
                }
                $result->results = $input['answer'];
		return $result;
	}
        
        private function updateStatus($project, $section, $answers) {
            if($project->type == 'incident'){
                return 'incident';
            }
            $section = (int) $section;
            //dd($answers);
            $section_qcount = $project->questions->where('section', $section)->count();
            
            $formulas = $project->sections[$section]->formula; 
            
            $anscount = count(array_filter(array_values(array_dot($answers)), 'strlen'));// dd($anscount);
            foreach($answers as $qnum => $answer){
                //get only first item in array because question with logical check will include only one answer
                $var[$qnum] = array_values($answer)[0];
                
                $q = $this->questions->getQuestionByQnum($qnum, $project->id);
                $ac = 0;
                foreach($answer as $akey => $aval){
                    if($akey != 'radio'){
                        $qanswer = $q->qanswers->where('akey', $akey)->first();
                        if(in_array($qanswer->type, ['checkbox', 'text']) && $anscount > 0){
                            $ac++;
                        }
                    }else{
                        $ac++;
                    }
                    
                }
                
            }
            
            if($anscount === 0 ){ 
                return 'missing';
            }elseif ($anscount < $section_qcount) {
                return 'incomplete';
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
            }elseif($anscount > $section_qcount && $ac > 0){
                return 'complete';
            }else{
                return 'unknown';
            }
            
           throw new GeneralException('Something wrong with status checking!');
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