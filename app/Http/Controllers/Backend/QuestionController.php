<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Project\Question\CreateQuestionRequest;
use App\Http\Requests\Backend\Project\Question\UpdateQuestionRequest;
use App\Repositories\Backend\Organization\OrganizationContract;
use App\Repositories\Backend\Project\ProjectContract;
use App\Repositories\Backend\Question\QuestionContract;
use Hash;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class QuestionController extends Controller
{
    protected $questions;
    
    protected $project;
    
    protected $organization;
    
    public function __construct(
            QuestionContract $questions,
            ProjectContract $project,
            OrganizationContract $organization
            ) {
        //$this->middleware('ajaxurl', ['only' => [
        //    'editall',
        //]]);
        $this->questions = $questions;
        $this->project = $project;
        $this->organization = $organization;
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($project)
    {
        if($project->validate == 'person'){
            $route = route('ajax.project.person', [$project->id, '{pcode}']);
        }elseif($project->validate == 'pcode'){
            $route = route('ajax.project.pcode', [$project->id, '{pcode}-'.$project->organization->id]);
        }elseif($project->validate == 'uec_code'){
            
        }
        javascript()->put([
			'url' => $route,
                        //'org' => 
		]);
        return view('backend.project.result.create')
                        ->withProject($project);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create($project)
    {
        //dd($project_id);
        javascript()->put([
			'index' => 1
		]);
        //$project_id = Input::get('p');
        //$current_project = $this->project->findOrThrowException($project_id, true);
        return view('backend.project.questions.create')
        ->withQuestions($this->questions->getAllQuestions('qnum', 'asc')->prepend(['none' => 'None']))
        ->withProject($project)
        ->withProjects($this->project->getAllProjects('name','asc',true))
        ->withOrganizations($this->organization->getAllOrganizations('name', 'asc', ['projects']));
    
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store($project, CreateQuestionRequest $request)
    {
        $this->questions->create(
			$request->all(),
			$project
		);
        return redirect()->route('admin.project.questions.editall', $project->id)->withFlashSuccess('The question was successfully created.');
        }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($project, $question)
    {
        
        //$question = $this->questions->findOrThrowException($id, true); //dd((array) $question->answers);
        $answers = count((array) $question->answers);
        if(empty($answers)){
            $answers = 1;
        }
        javascript()->put([
			'index' => $answers
		]);
        return view('backend.project.questions.edit')
        ->withQuestion($question)
        ->withQuestions($this->questions->getAllQuestions('qnum', 'asc')->prepend(['none' => 'None']))        
        ->withProjects($this->project->getAllProjects('name','asc',true))
        ->withOrganizations($this->organization->getAllOrganizations('name', 'asc', ['projects']));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function editall($project)
    {        
        // ajax route for sorting
        $route = route('ajax.project.questions.sort', [$project->id]);
        
        // ajax route for creating new question
        $add_question_url = route('ajax.project.question.new', [$project->id]);
        
        // get ajax urlhash for project
        $urlhash = $project->urlhash;
        
        // check if rehash need or not
        if (Hash::needsRehash($urlhash)) {
            // rehash if urlhash column in project table empty or invalid
            $urlhash = Hash::make($add_question_url);
            // update project table in database with new or correct urlhash
            $project->update(['urlhash' => $urlhash]);
        }
        /*
         * send javascript global object using helper function.
         * javascript global object is stored in variable named "ems" by default.
         * That variable can only be changed in config/javascript.php
         */
        javascript()->put([
			'url' => $route,
                        'add_question_url' => $add_question_url,
                        'urlhash' => $urlhash,
                        //'org' => 
		]);
        return view('backend.project.questions.editall')
                        ->withProject($project);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update($project, $question, UpdateQuestionRequest $request)
    {
        $this->questions->update($question,
			$request->except('project_id', 'organization'),
                        $project
                        
		);
		return redirect()->route('admin.project.questions.editall',[$project->id])->withFlashSuccess('The question was successfully updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($project, $question)
    {
        $this->questions->destroy($project, $question);
        return redirect()->route('admin.project.questions.editall',[$project->id])->withFlashSuccess('The question was successfully deleted.');
    }
}
