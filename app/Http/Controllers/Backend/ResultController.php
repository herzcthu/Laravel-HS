<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Project\Result\CreateResultRequest;
use App\Repositories\Backend\Location\LocationContract;
use App\Repositories\Backend\Organization\OrganizationContract;
use App\Repositories\Backend\Participant\ParticipantContract;
use App\Repositories\Backend\PLocation\PLocationContract;
use App\Repositories\Backend\Project\ProjectContract;
use App\Repositories\Backend\Question\QuestionContract;
use App\Repositories\Backend\Result\ResultContract;
use App\Repositories\Backend\User\UserContract;
use App\Status;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ResultController extends Controller
{
    protected $user;
    
    protected $organization;
    
    protected $project;
    
    protected $question;
    
    protected $participants;
    
    protected $results;
    
    protected $plocation;
    
    protected $location;
    
    protected $status;


    public function __construct(
            UserContract $user,
            OrganizationContract $organization,
            ProjectContract $project,
            QuestionContract $question,
            ParticipantContract $participants,
            PLocationContract $plocation,
            LocationContract $location,
            ResultContract $results,
            Status $status
            ) {
               $this->user = $user;
               $this->organization = $organization;
               $this->project = $project;
               $this->question = $question;
               $this->participants = $participants;
               $this->plocation = $plocation;
               $this->location = $location;
               $this->results = $results;
               $this->status = $status;
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($project, Request $request)
    {
        if($request->get('region')){
            $search_key = $request->get('region');
            
            $located = PLocation::where('org_id', $project->organization->id )->where('state',$search_key)->get();
        }
        if($request->get('district')){
            $search_key = $request->get('district');
            $located = PLocation::where('org_id', $project->organization->id )->where('district',$search_key)->get();
        }
        if($request->get('station')){
            $search_key = $request->get('station');
            $located = PLocation::where('org_id', $project->organization->id )->where('village',$search_key)->get();
        }
        if($request->get('status') && $request->get('section') >= 0){
            $section = $request->get('section');
            $search_key = $request->get('status');
            $located = PLocation::where('org_id', $project->organization->id )->OfwithAndWhereHas('results', function($query) use ($section, $search_key){
                    $query->where('information', $search_key)->where('section_id', (int)$section);
                
            })->get();
        }
        $alocations = PLocation::where('org_id', $project->organization->id )->get();
        if(isset($search_key)){
            $locations = $located;
            $sections = $project->sections;
        }else{
            $results = $project->results;
            $sections = $project->sections;
            //dd($project->organization->id);
            /**
            $locations = PLocation::where('org_id', $project->organization->id )->OfwithAndWhereHas('results', function($query) use ($project){
                    $query->where('project_id', $project->id);
                
            })->get();
             * 
             */
            $locations = $alocations;
        }
        
        return view('frontend.result.index')
                        ->withParticipants($this->participants)
                        ->withProject($project)
                        ->withSections($sections)
                        ->withLocations($locations)
                        ->withAllLoc($alocations);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create($project)
    {
        $user = auth()->user();
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
        return view('frontend.result.create')
			->withUser($user)
                        ->withProject($project);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store($project, $section_id = false, CreateResultRequest $request)
    {
        
        $this->results->create(
			$request->except('project_id'),
			$project,
                        $section_id
		);
        return redirect()->route('data.project.results.index', $project->id)->withFlashSuccess('The results was successfully created.');
        
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
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
