<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\Project\Result\CreateResultRequest;
use App\Participant;
use App\PLocation;
use App\Repositories\Backend\Participant\Role\RoleRepositoryContract;
use App\Repositories\Backend\Project\ProjectContract;
use App\Repositories\Frontend\Participant\ParticipantContract;
use App\Repositories\Frontend\PLocation\PLocationContract;
use App\Repositories\Frontend\Result\ResultContract;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StatusController extends Controller
{
    protected $project;
    
    protected $results;

    protected $participants;
    
    protected $proles;
    
    protected $plocation;

    public function __construct(ProjectContract $project,
                                ResultContract $results,
                                ParticipantContract $participants,
                                RoleRepositoryContract $proles,
                                PLocationContract $plocation) {
        $this->project = $project;
        $this->results = $results;
        $this->participants = $participants;
        $this->proles = $proles;
        $this->plocation = $plocation;
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($project, Request $request)
    {
                
        $alocations = PLocation::where('org_id', $project->organization->id )->get();
        //dd($alocations->lists('state', 'state'));
        
        return view('frontend.result.status-locations')
                        ->withProject($project)
                        //->withLocations($locations)
                        ->withAllLoc($alocations)
                        ->withRequest($request);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function response($project, Request $request)
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
        if($request->get('section') >= 0){
            $section = $request->get('section');
            $search_key = $request->get('status');
            if($search_key == 'missing'){

                $located = PLocation::where('org_id', $project->organization->id )->OfwithAndWhereHas('results', function($query) use ($section, $search_key){
                        $query->where('section_id', (int)$section)
                                ->whereNotIn('information',['complete', 'incomplete', 'error']);

                })->orNotWithResults()->get();
            }else{
                $located = PLocation::where('org_id', $project->organization->id )->OfwithAndWhereHas('results', function($query) use ($section, $search_key){
                        $query->where('information', $search_key)->where('section_id', (int)$section);

                })->get();
            }
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
        
        return view('frontend.result.response-locations')
                        ->withParticipants($this->participants)
                        ->withProject($project)
                        ->withSections($sections)
                        ->withLocations($locations)
                        ->withAllLoc($alocations);        
    }
    
    public function iresponse($project, Request $request) {
        
        $iresponseCol = \App\Question::where('project_id', $project->id)->where('qnum', config('aio.iresponse'))->first();
        $incidents = \App\Result::where('project_id', $project->id)->with('resultable')->get();
        $locations = \App\PLocation::where('org_id', $project->org_id)->with('results')->groupBy('state')
                ->ofWithAndWhereHas('answers', function($ans) use ($iresponseCol) {
                    $ans->where('qid', $iresponseCol->id);
                }); 
        return view('frontend.result.response-incident')
                ->withProject($project)
                ->withRequest($request)
                ->withQuestion($iresponseCol)
                ->withIncidents($incidents)
                ->withLocations($locations);
    }
}
