<?php

namespace App\Repositories\Frontend\Result;

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
    public function __construct(ProjectContract $project, OrganizationContract $organization, PLocationContract $pcode, ParticipantContract $participant, QuestionContract $questions) {
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

        if (!is_null($result))
            return $result;

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
    public function getResultBySection($section, $project, $resultable, $qnum, $anskey, $incident = '') {
        $result_ByNum = Result::where('project_id', $project)->where('section_id', $section)->where('resultable_id', $resultable);
        if (!empty($incident)) {      //dd($incident);
            $result = $result_ByNum->where('incident_id', (int) $incident)->first();
        } else {
            $result = $result_ByNum->first();
        }
        if (!is_null($result)) {
            if (!is_null($result->answers)) {
                foreach ($result->answers as $ans) {
                    if ($ans->akey == $anskey) {
                        return $ans->value;
                    }
                }
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
        $order_by = ((null !== Input::get('field')) ? Input::get('field') : $order_by);
        $sort = ((null !== Input::get('sort')) ? Input::get('sort') : $sort);
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
        $order_by = ((null !== Input::get('field')) ? Input::get('field') : $order_by);
        $sort = ((null !== Input::get('sort')) ? Input::get('sort') : $sort);
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
    public function create($input, $project) {
        $validate = $input['validator_id'];

        if ($project->validate == 'person') {
            $resultable = $this->participant->getParticipantByCode($validate, $project->organization->id);
            $resultable_type = 'App\Participant';
        } else {
            $resultable = $this->pcode->getLocationByPcode($validate, $project->organization->id);
            $resultable_type = 'App\PLocation';
        }

        foreach ($input['answer'] as $section => $answers) {
            if ($project->type == 'incident') {

                if (array_key_exists('incident_id', $input)) {
                    $incident_id = $input['incident_id'];
                } else {
                    // get last incident id
                    $incident = \App\Result::where('project_id', $project->id)
                                    ->where('section_id', $section)
                                    ->where('resultable_id', $resultable->id)
                                    ->orderBy('incident_id', 'desc')->first();
                    if (!is_null($incident)) {
                        $incident_id = $incident->incident_id + 1;
                    } else {
                        $incident_id = 1;
                    }
                }
            } elseif ($project->type == 'survey') {
                $incident_id = $input['form_id'];
                $incident = \App\Result::where('project_id', $project->id)
                                ->where('section_id', $section)
                                ->where('resultable_id', $resultable->id)
                                ->where('incident_id', $incident_id)->first();
                if (!is_null($incident) || empty($incident_id)) {

                    throw new GeneralException('Duplicate or No form ID! Please select another form ID or edit from list.');
                }
            } else {
                $incident_id = null;
            }
            $this->saveResults($project, $section, $answers, $incident_id, $resultable, $resultable_type);
        }
    }

    /**
     * @param $result
     * @param $input
     * @param $organizations
     * @return bool
     * @throws GeneralException
     */
    public function update($code, $input, $project, $form_id) {
        $validate = $input['validator_id'];
        if ($project->validate == 'person') {
            $resultable = $this->participant->getParticipantByCode($validate, $project->organization->id);
            $resultable_type = 'App\Participant';
        } else {
            $resultable = $this->pcode->getLocationByPcode($validate, $project->organization->id);
            $resultable_type = 'App\PLocation';
        }
        foreach ($input['answer'] as $section => $raw_answers) {
            // this function defined in app\helpers.php
            $answers = array_filter_recursive($raw_answers);
            if ($code instanceof \App\Result) {
                $result = $code;
            }
            if ($code instanceof \App\PLocation) {
                $result = $code->results->where('project_id', $project->id)
                                ->where('incident_id', (int) $form_id)
                                ->where('section_id', (int) $section)->first();
            }

            /**
             * To Do: need to implement later
             */
            if ($code instanceof \App\Participant) {
                $result = $code;
            }

            if (!is_null($result)) {
                if(!empty($answers)) {
                    // update if $result is not null and $answers is not empty
                    /**
                     * delete all related answers before save.
                     * More like overwriting old answers
                     */
                    Answers::where('status_id', $result->id)->delete();
                    foreach ($answers as $qslug => $ans) {
                        $q = $this->questions->getQuestionByQnum($qslug, $section, $project->id);
                        if (is_null($q)) {
                            throw new GeneralException("slug = $qslug, section = $section, project = $project->id There was a problem creating this result. Please try again.");
                        }
                        foreach ($ans as $akey => $aval) {
                            if ($akey == 'radio') {
                                $answerkey = $aval;
                            } else {
                                $answerkey = $akey;
                            }
                            $qanswer = $q->qanswers->where('slug', $answerkey)->first();
                            if (!is_null($qanswer) && in_array($qanswer->type, ['radio', 'checkbox'])) {
                                $answerVal = $qanswer->value;
                            } else {
                                $answerVal = $aval;
                            }

                            $answerR = Answers::firstOrNew(['qid' => $q->id, 'akey' => $answerkey, 'status_id' => $result->id]);

                            if (isset($answerVal) && !empty($answerVal)) {
                                $answerR->value = $answerVal;
                                $answerR->results()->dissociate();
                                $answerR->results()->associate($result);
                                $answerR->save();
                            }
                        }
                    }
                    $rinput['information'] = $this->updateStatus($project, $section, $answers, $form_id, $resultable, $resultable_type);
                    $result->update($rinput);
                } else {
                    $result->delete();
                }
                
            } else {                
                
                if (empty($form_id)) {
                    throw new GeneralException('No form ID! Please select another form ID or edit from list.');
                }
                
                //create new;

                $result = $this->saveResults($project, $section, $answers, $form_id, $resultable, $resultable_type);
            
            }
        }
        
        return $result;
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
    private function flushParent($parent_id, $result) {
        $result->parent()->dissociate();
        $parent = $this->findOrThrowException($parent_id);
        $result->parent()->associate($parent);
    }

    private function flushProject($project_id, $result) {
        $result->project()->dissociate();
        $project = $this->project->findOrThrowException($project_id);
        $result->project()->associate($project);
        //$result->save();
    }

    /**
     * @param $organizations
     * @throws GeneralException
     */
    private function checkResultOrganizationsCount($organizations) {
        //Result Updated, Update Organizations
        //Validate that there's at least one organization chosen
        if (count($organizations['assignees_organizations']) == 0)
            throw new GeneralException('You must choose at least one organization.');
    }

    /**
     * @param $input
     * @return mixed
     */
    private function createResultStub($input) {
        $result = new Result;
        $result->section_id = isset($input['section']) ? $input['section'] : null;
        //$result->report = isset($input['report'])?$input['report']:null;
        if (isset($input['incident_id'])) {
            $result->incident_id = $input['incident_id'];
        }
        $result->results = $input['answer'];
        return $result;
    }

    /**
     * Status check function
     * To Do: need to rewrite whole function
     * @param type $project
     * @param type $section
     * @param type $answers
     * @return string
     * @throws GeneralException
     */
    private function updateStatus($project, $section, $answers, $form_id) {

        if ($project->type == 'incident') {
            return 'incident';
        }
        $section = (int) $section;

        /**
         * Get total questions count in a section
         */
        $section_qcount = $project->questions->where('section', $section)->count();

        $anscount = count(array_filter(array_keys($answers), 'strlen')); // total answers count from form submit
        // initialize error status
        $error = false;
        // flat multi dimentional questions and answers array to single array
        $flat_answers = call_user_func_array('array_merge', $answers);
        /**
         * Loop through form submitted answers
         * $qnum string (question number)
         * $answer array
         */        
        
        foreach ($answers as $qslug => $answer) {
            // get question using qnum slug
            $lftq = $this->questions->getQuestionByQnum($qslug, $section, $project->id);
            
            /**
             * Loop answer
             * $akey string (radio or answer key)
             * $aval mixed (string or integer submitted by form input)
             */
            foreach ($answer as $akey => $aval) {  
                if ($akey == 'radio') {
                    $qanswer = $lftq->qanswers->where('slug', $aval)->first();
                } else {
                    $qanswer = $lftq->qanswers->where('slug', $akey)->first();
                }
                if (!is_null($qanswer)) {
                    if (!empty($qanswer->logic)) {
                        if (!empty($qanswer->logic['lftval'])) {
                            $lftval = $qanswer->logic['lftval'];
                        } else {
                            switch ($qanswer->type) {
                                case 'radio':
                                    $lftval = $qanswer->value;
                                    break;
                                case 'checkbox':
                                    $lftval = $qanswer->value;
                                    break;
                                default:
                                    if (array_key_exists($qanswer->logic['lftans'], $flat_answers)) {
                                        /** get actual answer if exist in input
                                         *  Ofcourse it will exist because it is left side answer
                                         */
                                        $lftval = $flat_answers[$qanswer->logic['lftans']];
                                    } else {
                                        // get answer from database
                                    }
                                    break;
                            }
                        }
                        if (!empty($qanswer->logic['rftval'])) {
                            $rftval = $qanswer->logic['rftval'];
                        } else {
                            // initialize right side value
                            $rftval = '';
                            // get right side question
                            if(isset($qanswer->logic['rftquess'])) {
                                
                                $rftq = $this->questions->getQuestionByQnum($qanswer->logic['rftquess'], $section, $project->id);
                                if (!is_null($rftq)) {
                                    if(isset($qanswer->logic['rftans'])) {
                                    $rftanswer = $rftq->qanswers->where('slug', $qanswer->logic['rftans'])->first();

                                        switch ($rftanswer->type) {
                                        case 'radio':
                                            $rftval = $rftanswer->value;
                                            break;
                                        case 'checkbox':
                                            $rftval = $rftanswer->value;
                                            break;
                                        default:
                                            if (array_key_exists($qanswer->logic['rftans'], $flat_answers)) {
                                                /** get actual answer if exist in input
                                                 */
                                                $rftval = $flat_answers[$qanswer->logic['rftans']];
                                            } else {
                                                // get answer from database
                                                $results = Result::where('project_id', $project->id)
                                                        ->where('incident_id', $form_id)
                                                        ->where('resultable_id', $resultable->id)
                                                        ->where('resultable_type', $resultable_type)->all();
                                                dd($results);
                                            }
                                            break;
                                    }
                                    }
                                }
                            }
                        }

                        switch ($qanswer->logic['operator']) {
                            case '=':
                                if ($lftval != $rftval)
                                    $error = true;
                                break;
                            case '>':
                                if ($lftval < $rftval)
                                    $error = true;
                                break;
                            case '<':
                                if ($lftval > $rftval)
                                    $error = true;
                                break;
                            case 'between':
                                if ($lftval > $qanswer->logic['minval'] || $lftval < $qanswer->logic['maxval'])
                                    $error = true;
                                break;

                            default:
                                $error = false;
                                break;
                        }
                    }
                }
            }
        }
        //dd($ac);
        if ($error === true) {
            return 'error';
        } elseif ($anscount === 0) {
            return 'missing';
        } elseif ($anscount >= $section_qcount) {
            return 'complete';
        } elseif ($anscount < $section_qcount) {
            return 'incomplete';
        } else {
            return 'unknown';
        }

        throw new GeneralException('Something wrong with status checking!');
    }

    private function logic($formula, $variables) {
        //dd($variables);
        $compiler = new Compiler(); //dd($formula);
        try {
            $executable = $compiler->compile($formula);
            $result = $executable->run($variables);
        } catch (ParserException $e) {
            throw new GeneralException('Logical check formula error!');
        }
        return $result;
    }

    private function updateResults($project, $result, $section, $answers) {
        try {
            $results = $result->results;
            foreach ($results as $sec => $rs) {
                foreach ($rs as $rk => $res) {
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
    
    
    /**
     * @param type $project
     * @param type $section
     * @param type $answers
     * @param type $incident_id
     * @param type $resultable
     * @param type $resultable_type
     * @return type
     * @throws GeneralException
     */
    private function saveResults($project, $section, $raw_answers, $incident_id, $resultable, $resultable_type) {
        
        // this function defined in app\helpers.php
        $answers = array_filter_recursive($raw_answers);
        /**
         * Result is link between questions,location,paricipants and actual answer
         * mark the status
         * Results table is polymophic table between Participants, Locations and Results
         */
        $result = Result::firstOrNew(['section_id' => $section, 'project_id' => $project->id,
                    'incident_id' => $incident_id,
                    'resultable_id' => $resultable->id,
                    'resultable_type' => $resultable_type]);
        if ($incident_id) {
            $result->incident_id = $incident_id;
        }
        $result->resultable_id = $resultable->id;
        $result->results = $answers;
        $result->section_id = $section;
        $current_user = auth()->user();
        $result->user()->associate($current_user);

        $result->project()->associate($project);
        if (isset($resultable)) {
            $result->resultable()->associate($resultable);
        }
        
        if(!empty($answers)) {
            $result->information = $this->updateStatus($project, $section, $answers, $incident_id, $resultable, $resultable_type);
        
            if ($result->save()) {
                /**
                 * Save actual answers after result status saved
                 * delete all related answers before save.
                 * More like overwriting old answers
                 */
                Answers::where('status_id', $result->id)->delete();
                foreach ($answers as $qslug => $ans) {
                    $q = $this->questions->getQuestionByQnum($qslug, $section, $project->id);
                    foreach ($ans as $akey => $aval) {
                        if ($akey == 'radio') {
                            $answerkey = $aval;
                        } else {
                            $answerkey = $akey;
                        }
                        $qanswer = $q->qanswers->where('slug', $answerkey)->first();
                        if (!is_null($qanswer)) {
                            if (in_array($qanswer->type, ['radio', 'checkbox'])) {
                                $answerVal = $qanswer->value;
                            } else {
                                $answerVal = $aval;
                            }

                            $answerR = Answers::firstOrNew(['qid' => $q->id, 'akey' => $answerkey, 'status_id' => $result->id]);
                            if (isset($answerVal) && !empty($answerVal)) {
                                $answerR->value = $answerVal;
                                $result->answers()->save($answerR);
                            }
                        }
                    }
                }
                        
                return $result;
            }
        } 
        
    }

}
