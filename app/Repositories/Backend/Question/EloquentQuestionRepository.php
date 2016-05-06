<?php namespace App\Repositories\Backend\Question;

use App\Exceptions\Backend\Access\Question\QuestionNeedsOrganizationsException;
use App\Exceptions\GeneralException;
use App\Question;
use App\Repositories\Backend\Organization\OrganizationContract;
use App\Repositories\Backend\Project\ProjectContract;
use App\Repositories\Frontend\Auth\AuthenticationContract;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Input;

/**
 * Class EloquentQuestionRepository
 * @package App\Repositories\Question
 */
class EloquentQuestionRepository implements QuestionContract {

        protected $project;
        /**
	 * @var OrganizationContract
	 */
	protected $organization;

	/**
	 * @var AuthenticationContract
	 */
	protected $auth;

	/**
	 * @param OrganizationRepositoryContract $organization
	 * @param AuthenticationContract $auth
	 */
	public function __construct(ProjectContract $project, OrganizationContract $organization, AuthenticationContract $auth) {
		$this->project = $project;
                $this->organization = $organization;
		$this->auth = $auth;
	}

	/**
	 * @param $id
	 * @param bool $withOrganizations
	 * @return mixed
	 * @throws GeneralException
	 */
	public function findOrThrowException($id, $withProject = false) {
		if ($withProject)
			$question = Question::with('project')->find($id);
		else
			$question = Question::find($id);

		if (! is_null($question)) return $question;

		throw new GeneralException('That question does not exist.');
	}
        
        public function getQuestionByQnum($qslug, $section, $project){
            return Question::where('slug', $qslug)
                    ->where('section', $section)
                    ->where('project_id', $project)
                    ->first();
            
        }

	/**
	 * @param $per_page
	 * @param string $order_by
	 * @param string $sort
	 * @param int $status
	 * @return mixed
	 */
	public function getQuestionsPaginated($per_page, $order_by = 'id', $sort = 'asc') {
                $order_by = ((null !== Input::get('field'))? Input::get('field'):$order_by);
                $sort = ((null !== Input::get('sort'))? Input::get('sort'):$sort);
		return Question::orderBy($order_by, $sort)->paginate($per_page);
	}
        
        /**
	 * @param $per_page
	 * @param string $order_by
	 * @param string $sort
	 * @param int $status
	 * @return mixed
	 */
	public function searchQuestions($queue, $status = 1, $order_by = 'id', $sort = 'asc') {
            $order_by = ((null !== Input::get('field'))? Input::get('field'):$order_by);
            $sort = ((null !== Input::get('sort'))? Input::get('sort'):$sort);
            return Question::where('status', $status)->orderBy($order_by, $sort)->search($queue)->get();
	}

	/**
	 * @param $per_page
	 * @return Paginator
	 */
	public function getDeletedQuestionsPaginated($per_page) {
		return Question::onlyTrashed()->paginate($per_page);
	}

	/**
	 * @param string $order_by
	 * @param string $sort
	 * @return mixed
	 */
	public function getAllQuestions($order_by = 'id', $sort = 'asc') {
		return Question::orderBy($order_by, $sort)->get();
	}

	/**
	 * @param $input
	 * @param $organizations
	 * @param $permissions
	 * @return bool
	 * @throws GeneralException
	 * @throws QuestionNeedsOrganizationsException
	 */
	public function create($input, $project, $ajax = false) {
                $question = $this->createQuestionStub($input);
                if(isset($input['related_data'])) {
                    $related_id = $input['related_data']['q'];
                    if(!empty($related_id)){
                        $related = $this->findOrThrowException($related_id);

                        $question->parent()->associate($related);
                    }
                }
                //$question->answers = $input['answers'];
                
		$question->project()->associate($project);
                
                if ($question->save()) {
                    $question->sort = $question->id;
                    $question->update();
                    \App\QAnswers::where('qid', $question->id)->delete();
                    foreach($input['answers'] as $k => $av){
                        $qanswer = \App\QAnswers::firstOrNew(['qid' => $question->id, 'akey' => $k]);
                        $qanswer->akey = $k;
                        if(isset($av['type']))
                            $qanswer->type = $av['type'];
                        if(isset($av['text']))
                            $qanswer->text = $av['text'];
                        if(isset($av['value']))
                            $qanswer->value = $av['value'];
                        if(isset($av['require']))
                            $qanswer->qarequire = $av['require'];
                        if(isset($av['optional']))
                            $qanswer->optional = $av['optional'];
                        if(isset($av['css']))
                            $qanswer->css = $av['css'];
                        $question->qanswers()->save($qanswer);
                    }
                    if($ajax == true){
                    // need to return $question for ajax
			return $question;
                    }else{
                        return true;
                    }
		}

		throw new GeneralException('There was a problem creating this question. Please try again.');
	}

	/**
	 * @param $id
	 * @param $input
	 * @param $organizations
	 * @return bool
	 * @throws GeneralException
	 */
	public function update($question, $input, $project, $ajax = false) {
            dd($question);
		$related_id = (isset($input['related_data'])) ? $input['related_data']['q']:'';
                if(!empty($related_id)){
                    $this->flushParent($related_id, $question);
                }
                if(isset($input['report']) && ($input['report'] == 'none' || empty($input['report']))) {
                    $input['report'] = null;
                } 
                //$question->answers = $input['answers'];
                //$question->related_data = $input['related_data'];
                //$question->answer_view = $input['answer_view'];
                $input['sameanswer'] = isset($input['sameanswer']) ? 1 : 0;
                $input['display']['qnum'] = isset($input['display']['qnum'])? 1 : 0;
                $input['display']['question'] = isset($input['display']['question'])? 1 : 0;
                
		if ($question->update($input)) { 
                    if(array_key_exists('answers', $input)) {
                        \App\QAnswers::where('qid', $question->id)->delete();
                        foreach($input['answers'] as $k => $av){
                            $qanswer = \App\QAnswers::firstOrNew(['qid' => $question->id, 'akey' => $k]);
                            $qanswer->akey = $k;
                            $qanswer->type = $av['type'];
                            $qanswer->text =  htmlspecialchars($av['text'], ENT_QUOTES);
                            $qanswer->value = $av['value'];
                            $qanswer->qarequire = (isset($av['require']))?$av['require']:'';
                            $qanswer->css = (isset($av['css']))?$av['css']:'';
                            $question->qanswers()->save($qanswer);
                        }
                    } else {
                        \App\QAnswers::where('qid', $question->id)->delete();
                    }
                    if($ajax == true){
                    // need to return $question for ajax
			return $question;
                    }else{
                        return true;
                    }
		}

		throw new GeneralException('There was a problem updating this question. Please try again.');
	}

        public function addLogic($project,$question,$input, $ajax = false) {
            $qans = $question->qanswers;
            $logic = [];
            foreach($qans as $ans){
                if($ans->slug == $input['lftans']){
                    if($input['operator'] == 'delete') {
                        $logic[] = $ans->update(['logic' => null]);
                    } else {
                        $logic[] = $ans->update(['logic' => $input]);
                    }
                } 
                
            }
            return $logic;
        }
	
	/**
	 * @param $id
	 * @return bool
	 * @throws GeneralException
	 */
	public function destroy($project, $question) {
		$question->qanswers()->delete();
                $question->ans()->delete();
                $question->children()->delete();
		if ($question->delete())
			return true;

		throw new GeneralException("There was a problem deleting this question. Please try again.");
	}

	/**
	 * @param $id
	 * @return boolean|null
	 * @throws GeneralException
	 */
	public function delete($project, $question) {
		//$question = $this->findOrThrowException($id, true);

		//Detach all organizations & permissions
		$question->detachOrganizations($question->organizations);
		$question->detachPermissions($question->permissions);

		try {
			$question->forceDelete();
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
		$question = $this->findOrThrowException($id);

		if ($question->restore())
			return true;

		throw new GeneralException("There was a problem restoring this question. Please try again.");
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

		$question = $this->findOrThrowException($id);
		$question->status = $status;

		if ($question->save())
			return true;

		throw new GeneralException("There was a problem updating this question. Please try again.");
	}

	/**
	 * Check to make sure at lease one organization is being applied or deactivate question
	 * @param $question
	 * @param $organizations
	 * @throws QuestionNeedsOrganizationsException
	 */
	private function validateOrganizationAmount($question, $organizations) {
		//Validate that there's at least one organization chosen, placing this here so
		//at lease the question can be updated first, if this fails the organizations will be
		//kept the same as before the question was updated
		if (count($organizations) == 0) {
			//Deactivate question
			$question->status = 0;
			$question->save();

			$exception = new QuestionNeedsOrganizationsException();
			$exception->setValidationErrors('You must choose at lease one organization. Question has been created but deactivated.');

			//Grab the question id in the controller
			$exception->setQuestionID($question->id);
			throw $exception;
		}
	}

	/**
	 * @param $input
	 * @param $question
	 * @throws GeneralException
	 */
	private function checkQuestionByEmail($input, $question)
	{
		//Figure out if email is not the same
		if ($question->email != $input['email'])
		{
			//Check to see if email exists
			if (Question::where('email', '=', $input['email'])->first())
				throw new GeneralException('That email address belongs to a different question.');
		}
	}

	/**
	 * @param $organizations
	 * @param $question
	 */
	private function flushParent($parent_id, $question)
	{
		$question->parent()->dissociate();
                $parent = $this->findOrThrowException($parent_id);
                $question->parent()->associate($parent);
	}

	private function flushProject($project_id, $question)
	{
		$question->project()->dissociate();
                $project = $this->project->findOrThrowException($project_id);
                $question->project()->associate($project);
                //$question->save();
	}

	/**
	 * @param $organizations
	 * @throws GeneralException
	 */
	private function checkQuestionOrganizationsCount($organizations)
	{
		//Question Updated, Update Organizations
		//Validate that there's at least one organization chosen
		if (count($organizations['assignees_organizations']) == 0)
			throw new GeneralException('You must choose at least one organization.');
	}

	/**
	 * @param $input
	 * @return mixed
	 */
	private function createQuestionStub($input)
	{
		$question = new Question;
                if(isset($input['section']))
                $question->section = $input['section'];
                
                if(isset($input['report']))
                    $question->report = $input['report'];
                
		$question->qnum = $input['qnum'];
		$question->question = htmlspecialchars($input['question'], ENT_QUOTES);
                
                if(isset($input['related_data']))
                    $question->related_data = $input['related_data'];
                
                $question->answers = $input['answers'];
                
                if(isset($input['display']))
                    $question->display = $input['display'];
                
                if(isset($input['answer_view']))
                    $question->answer_view = $input['answer_view'];
                
                if(isset($input['sameanswer']))
                    $question->sameanswer = isset($input['sameanswer']) ? 1 : 0;
                
		return $question;
	}
}