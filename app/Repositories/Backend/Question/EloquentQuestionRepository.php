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
        
        public function getQuestionByQnum($qnum){
            $question_ByNum = Question::where('qnum', $qnum)->first();
            $question = $this->findOrThrowException($question_ByNum->id);
            return $question;
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
	public function create($input, $project) {
                $question = $this->createQuestionStub($input);
                $related_id = $input['related_data']['q'];
                if(!empty($related_id)){
                    $related = $this->findOrThrowException($related_id);
                           
                    $question->parent()->associate($related);
                }
                //$question->answers = $input['answers'];
                
		$question->project()->associate($project);
                
                if ($question->save()) {
                    \App\QAnswers::where('qid', $question->id)->delete();
                    foreach($input['answers'] as $k => $av){
                        $qanswer = \App\QAnswers::firstOrNew(['qid' => $question->id, 'akey' => $k]);
                        $qanswer->akey = $k;
                        $qanswer->type = $av['type'];
                        $qanswer->text = $av['text'];
                        $qanswer->value = $av['value'];
                        $qanswer->require = $av['require'];
                        $question->qanswers()->save($qanswer);
                    }
			return true;
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
	public function update($question, $input, $project) {
		$related_id = $input['related_data']['q'];
                if(!empty($related_id)){
                    $this->flushParent($related_id, $question);
                }
                $question->section = $input['section'];
                if($input['report'] == 'none' || empty($input['report'])) {
                    $question->report = null;
                } else {
                    $question->report = $input['report'];
                }
                //$question->answers = $input['answers'];
                //$question->related_data = $input['related_data'];
                //$question->answer_view = $input['answer_view'];
                $question->sameanswer = isset($input['sameanswer']) ? 1 : 0;
                $display['qnum'] = isset($input['display']['qnum'])? 1 : 0;
                $display['question'] = isset($input['display']['question'])? 1 : 0;
                $question->display = $display;
                
		if ($question->whereQnum($question->qnum)->whereProjectId($project['project_id'])->first()->update($input)) { 
                    \App\QAnswers::where('qid', $question->id)->delete();
                    foreach($input['answers'] as $k => $av){
                        $qanswer = \App\QAnswers::firstOrNew(['qid' => $question->id, 'akey' => $k]);
                        $qanswer->akey = $k;
                        $qanswer->type = $av['type'];
                        $qanswer->text = $av['text'];
                        $qanswer->value = $av['value'];
                        $qanswer->require = $av['require'];
                        $question->qanswers()->save($qanswer);
                    }
                    return true;
		}

		throw new GeneralException('There was a problem updating this question. Please try again.');
	}

	
	/**
	 * @param $id
	 * @return bool
	 * @throws GeneralException
	 */
	public function destroy($id) {
		if (auth()->id() == $id)
			throw new GeneralException("You can not delete yourself.");

		$question = $this->findOrThrowException($id);
		if ($question->delete())
			return true;

		throw new GeneralException("There was a problem deleting this question. Please try again.");
	}

	/**
	 * @param $id
	 * @return boolean|null
	 * @throws GeneralException
	 */
	public function delete($id) {
		$question = $this->findOrThrowException($id, true);

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
                $question->section = $input['section'];
                $question->report = $input['report'];
		$question->qnum = $input['qnum'];
		$question->question = $input['question'];
                $question->related_data = $input['related_data'];
                $question->answers = $input['answers'];
                if(isset($input['display']))
                    $question->display = $input['display'];
                $question->answer_view = $input['answer_view'];
                $question->sameanswer = isset($input['sameanswer']) ? 1 : 0;
		return $question;
	}
}