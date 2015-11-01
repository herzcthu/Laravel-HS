<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\Backend\Project\Question\CreateQuestionRequest;
use App\Http\Requests\Backend\Project\Question\UpdateQuestionRequest;
use App\Repositories\Backend\Organization\OrganizationContract;
use App\Repositories\Backend\Project\ProjectContract;
use App\Repositories\Backend\Question\QuestionContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
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
			'index' => 0
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
        $answers = count((array) $question->answers) - 1; 
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
                        $request->only('project_id')
                        
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
