<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Project\CreateProjectRequest;
use App\Http\Requests\Backend\Project\UpdateProjectRequest;
use App\Repositories\Backend\Organization\OrganizationContract;
use App\Repositories\Backend\Project\ProjectContract;
use App\Repositories\Backend\Question\QuestionContract;
use App\Repositories\Backend\Result\ResultContract;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProjectController extends Controller
{
    protected $projects;
    
    protected $organizations;

    protected $questions;
    
    protected $results;


    public function __construct(
            ProjectContract $projects,
            OrganizationContract $organizations,
            QuestionContract $questions,
            ResultContract $results
            ) {
        $this->projects = $projects;
        $this->organizations = $organizations;
        $this->questions = $questions;
        $this->results = $results;
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
        return view('backend.project.index')
			->withProjects($this->projects->getProjectsPaginated(config('access.projects.default_per_page')));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        javascript()->put([
			'sectindex' => 0,
                        'reportindex' => 0
		]);
        return view('backend.project.create')
        ->withProjects($this->projects->getAllProjects('name','asc',true))
        ->withOrganizations($this->organizations->getAllOrganizations('name', 'asc', ['projects']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(CreateProjectRequest $request) {
            //dd($request->all());
		$this->projects->create(
			$request->except('organization'),
			$request->only('organization')
		);
		return redirect()->route('admin.projects.index')->withFlashSuccess('The project was successfully created.');
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
    public function edit($project)
    {
        //$project = $this->projects->findOrThrowException($id, true, true);
        
        $sections = count($project->sections) - 1;
        $report = count($project->reporting) - 1;
        javascript()->put([
			'sectindex' => $sections,
                        'reportindex' => $report
		]);
        
        return view('backend.project.edit')
                        ->withProject($project)
			->withOrganizations($this->organizations->getAllOrganizations('name', 'asc'))
			->withProjects($this->projects->getAllProjects('name', 'asc'))
                        ->withProjectOrganization($project->organization->pluck('id'))
			->withProjectQuestions($project->questions->lists('id')->all());
                
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(UpdateProjectRequest $request, $project)
    {
        
        $this->projects->update($project,
			$request->except('project', 'organization'),
                        $request->only('project'),
                        $request->only('organization')
                        
		);
		return redirect()->route('admin.projects.index')->withFlashSuccess('The project was successfully updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($project)
    {
        //
    }
    
    public function analysis($project){
        $questions = $project->questions;
        //dd($project->answersQ);
        $results = $this->results->getAllResults($project->id);
        /**
        return $results->each(function($item, $key) use ($questions){
            $qnum = $questions->each(function($qs, $qk) use ($item) {
                foreach($item as $q => $a){
                    if($q == $qs->qnum){
                        //dd($a);
                    }
                }
            });
            //dd($qnum);
        });
        $sections = $project->results->groupBy('section_id');
        foreach ($sections as $section){
           // dd($section);
        }
         * 
         */
        $located = \App\PLocation::where('org_id', $project->organization->id );
        return view('backend.project.analysis')
                    ->withProject($project)
                    ->withQuestions($questions)
                    ->withLocations($located);
    }
    
    public function export($project) {
        $file = $this->projects->export($project);
    }
    
    private function getResults($param) {
        
    }
    
}
