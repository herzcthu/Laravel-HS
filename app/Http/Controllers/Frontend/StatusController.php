<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\PLocation;
use App\Question;
use App\Repositories\Backend\Participant\Role\RoleRepositoryContract;
use App\Repositories\Backend\Project\ProjectContract;
use App\Repositories\Frontend\Participant\ParticipantContract;
use App\Repositories\Frontend\PLocation\PLocationContract;
use App\Repositories\Frontend\Result\ResultContract;
use App\Result;
use DB;
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
        $sections = $project->sections;
        return view('frontend.result.response-locations')
                        ->withRequest($request)
                        ->withProject($project)
                        ->withSections($sections);        
    }
    /*
     * To Do: need to add option in project setting which question number to show
     * in incident response page.
     */
    public function iresponse($project, Request $request) {
        $iresponseCol = Question::where('project_id', $project->id)->where('qnum', config('aio.iresponse'))->first();
        
        $dbraw = DB::select(DB::raw("SELECT pcode.state,answers.*,q.* 
            FROM pcode INNER JOIN results ON results.resultable_id = pcode.primaryid 
            INNER JOIN answers ON answers.status_id = results.id 
            INNER JOIN ( SELECT id,qnum FROM questions where id = '$iresponseCol->id') q ON q.id = answers.qid")); 
        $dbGroupBy = DB::select(DB::raw("SELECT pcode.state,answers.*,q.* 
            FROM pcode INNER JOIN results ON results.resultable_id = pcode.primaryid 
            INNER JOIN answers ON answers.status_id = results.id 
            INNER JOIN ( SELECT id,qnum FROM questions where id = '$iresponseCol->id') q ON q.id = answers.qid GROUP BY results.id"));
        
        $incidents = Result::where('project_id', $project->id);
        $locations = PLocation::where('org_id', $project->org_id)->groupBy('state');
        return view('frontend.result.response-incident')
                ->withProject($project)
                ->withRequest($request)
                ->withQuestion($iresponseCol)
                ->withIncidents($incidents)
                ->withLocations($locations)
                ->withDbraw(collect($dbraw))
                ->withDbGroup(collect($dbGroupBy));
    }
}
