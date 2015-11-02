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

class ResultController extends Controller
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
        /**
        $located = PLocation::where('org_id', $project->organization->id );
        
        
        if($request->get('region')){
            $state = $request->get('region');
            
            $located->where('state',$state);
        }
        if($request->get('district')){
            $district = $request->get('district');
            $located->where('district',$district);
        }
        if($request->get('station')){
            $station = $request->get('station');
            $located->where('village',$station);
        }
        
        if($request->get('section') >= 0){
            $section = $request->get('section');
            $status = $request->get('status');
            if($status == 'missing'){

                $located->OfwithAndWhereHas('results', function($query) use ($section, $status){
                        $query->where('section_id', (int)$section)
                                ->whereNotIn('information',['complete', 'incomplete', 'error']);
                })->orNotWithResults();
            }else{
                
                $located->OfwithAndWhereHas('results', function($query) use ($section, $status){
                        $query->where('information', $status)->where('section_id', (int)$section);
                });
            }
            
        }
        
        $locations = $located->get();
         * 
         */
        $sections = $project->sections;
        $alocations = PLocation::where('org_id', $project->organization->id )->get();
        return view('frontend.result.index-locations')
                        ->withParticipants($this->participants)
                        ->withProject($project)
                        ->withSections($sections)
                        //->withLocations($locations)
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
    { //dd($request->all());
        if($section_id == 'incident'){
            $section_id = $request->get('qnum');
        }
        $result = $this->results->create(
			$request->except('project_id'),
			$project,
                        $section_id
		);
        
        
        if($project->type == 'incident'){
            return redirect()->route('data.project.results.edit', [$project->id, $result->id])->withFlashSuccess('The results was successfully created.');
        }else{
            return redirect()->back()->withFlashSuccess('The results was successfully created.');
            //return redirect()->route('data.project.status.index', $project->id)->withFlashSuccess('The results was successfully created.');
        }
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
    public function edit($project, $code, Request $request)
    {
        $user = auth()->user();
        if($project->validate == 'person'){
            $route = route('ajax.project.person', [$project->id, '{pcode}']);
        }elseif($project->validate == 'pcode'){
            $route = route('ajax.project.pcode', [$project->id, '{pcode}-'.$project->organization->id]);
        }elseif($project->validate == 'uec_code'){
            
        }
        if($code instanceof PLocation){
            $validated = $this->formValidatePcode($project, $code, $request);
            $validated['validator'] = $code->pcode;
            $validated['validator_key'] = $code->pcode.'-'.$project->organization->id;
        }
        if($code instanceof Participant){
            $validated = $this->formValidatePerson($project, $code, $request);
            $validated['validator'] = $code->participant_id;
            $validated['validator_key'] = $code->participant_id;
        }
        if($code instanceof \App\Result){
            $validated = $this->formValidatePcode($project, $code->resultable, $request);
            $validated['validator'] = $code->resultable->pcode;
            $validated['validator_key'] = $code->resultable->pcode.'-'.$project->organization->id;
        }
        javascript()->put([
			'url' => $route,
                        //'org' => 
		]); //dd($code);
        return view('frontend.result.edit')
			->withUser($user)
                        ->withProject($project)
                        ->withValidated($validated)
                        ->withCode($code)
                        ->withResults($this->results);
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
    
    private function formValidatePerson($project, $person, Request $request) {
        $roles = $this->proles->getAllRoles();
        foreach ($roles as $role){
            if($role->level == 4){
                $prole['State'] = $role->id;
            }
            if($role->level == 3){
                $prole['District'] = $role->id;
            }
            if($role->level == 2){
                $prole['Township'] = $role->id;
            }
            if($role->level == 1){
                $prole['VTract'] = $role->id;
            }
            if($role->level == 0 ){
                $prole['Village'] = $role->id;
            }
        }
        
        foreach ($prole as $key => $val){
            if($key == 'State'){
                $located = $this->plocation->getStatesScope($person->pcode->state, 'village')
                    ->where('org_id', $project->organization->id)
                    ->where('role_id', $val);
            }elseif($key == 'District'){
                $located = $this->plocation->getDistrictsScope($person->pcode->district, 'village')
                    ->where('org_id', $project->organization->id)
                    ->where('role_id', $val);
            }elseif($key == 'Township'){
                $located = $this->plocation->getTownshipsScope($person->pcode->township, 'village')
                    ->where('org_id', $project->organization->id)
                    ->where('role_id', $val);
            }elseif($key == 'VTract'){
                $located = $this->plocation->getVTractsScope($person->pcode->village_tract, 'village')
                    ->where('org_id', $project->organization->id)
                    ->where('role_id', $val);
            }else{
                $located = $this->plocation->getVillagesScope($person->pcode->village, 'village')
                    ->where('org_id', $project->organization->id)
                    ->where('role_id', $val);
            }
            
            if(!$located->get()->isEmpty()){
                $locatedMembers = $located->first()->participants;
                //dd($village_tractMembers->count());
                foreach ($locatedMembers as $key => $sM){
                    $sM_id = str_replace($person->pcode->pcode, '', $sM->participant_id); //dd($sM->role->id);
                    if($person->role->id != $sM->role->id){
                        $response[$sM->role->name ] = $sM->name;
                    
                        $response[$sM->role->name.' ID'] = $sM->participant_id;
                    }
                    if($person->id == $sM->participant_id ){
                    $response[$sM->role->name.' '.$sM_id ] = $sM->name;
                    //}
                    $response[$sM->role->name.' '.$sM_id.' ID'] = $sM->participant_id;
                    }
                }
            }
        }
        
        //$observer_id = str_replace($person->pcode->pcode, '', $person->participant_id);
        $response[$person->role->name] = $person->name;
        $response[$person->role->name] = $person->participant_id;
        $response['Location ID'] = $person->pcode->pcode;
        if(!is_null($person->pcode->village)){
        $response['Village'] = $person->pcode->village;
        }
        if(!is_null($person->pcode->village_tract)){
        $response['Village Tract'] = $person->pcode->village_tract;
        }
        if(!is_null($person->pcode->township)){
        $response['Township'] = $person->pcode->township;
        }
        if(!is_null($person->pcode->district)){
        $response['District'] = $person->pcode->district;
        }
        if(!is_null($person->pcode->state)){
        $response['State'] = $person->pcode->state;
        }
        return $response;
    }


    
    public function formValidatePcode($project, $pcode, Request $request) {
        $roles = $this->proles->getAllRoles();
        foreach ($roles as $role){
            if($role->level == 4){
                $prole['State'] = $role->id;
            }
            if($role->level == 3){
                $prole['District'] = $role->id;
            }
            if($role->level == 2){
                $prole['Township'] = $role->id;
            }
            if($role->level == 1){
                $prole['VTract'] = $role->id;
            }
            if($role->level == 0 ){
                $prole['Village'] = $role->id;
            }
        }
        
        foreach ($prole as $key => $val){
            if($key == 'State'){
                $located = $this->plocation->getStatesScope($pcode->state, 'village')
                    ->where('org_id', $project->organization->id)
                    ->where('role_id', $val);
            }elseif($key == 'District'){
                $located = $this->plocation->getDistrictsScope($pcode->district, 'village')
                    ->where('org_id', $project->organization->id)
                    ->where('role_id', $val);
            }elseif($key == 'Township'){
                $located = $this->plocation->getTownshipsScope($pcode->township, 'village')
                    ->where('org_id', $project->organization->id)
                    ->where('role_id', $val);
            }elseif($key == 'VTract'){
                $located = $this->plocation->getVTractsScope($pcode->village_tract, 'village')
                    ->where('org_id', $project->organization->id)
                    ->where('role_id', $val);
            }else{
                $located = $this->plocation->getVillagesScope($pcode->village, 'village')
                    ->where('org_id', $project->organization->id)
                    ->where('role_id', $val);
            }
            
            if(!$located->get()->isEmpty()){
                $locatedMembers = $located->first()->participants;
                //dd($village_tractMembers->count());
                foreach ($locatedMembers as $key => $sM){
                    $sM_id = str_replace($pcode->pcode, '', $sM->participant_id);
                    if(!is_null($sM->supervisor)){
                        $response[$sM->supervisor->role->name] = $sM->supervisor->name;
                    }
                    $response[$sM->role->name.' '.$sM_id ] = $sM->name;
                    //}
                    $response[$sM->role->name.' '.$sM_id.' ID'] = $sM->participant_id;
                }
            }
        }
        /**
        $observers = $pcode->participants;
        foreach ($observers as $obk => $obv){
            $observer_id = str_replace($pcode->pcode, '', $obv->participant_id);
            if($obv->name != 'No Name'){
            $response[$obv->role->name.' '.$observer_id ] = $obv->name;
            $response[$obv->role->name.' ID' ] = $obv->participant_id;
            }
        }
         * 
         */
        $response['Location ID'] = $pcode->pcode;
        if(!is_null($pcode->village)){
        $response['Village'] = $pcode->village;
        }
        if(!is_null($pcode->village_tract)){
        $response['Village Tract'] = $pcode->village_tract;
        }
        if(!is_null($pcode->township)){
        $response['Township'] = $pcode->township;
        }
        if(!is_null($pcode->district)){
        $response['District'] = $pcode->district;
        }
        if(!is_null($pcode->state)){
        $response['State'] = $pcode->state;
        }
        return $response;
    }
}
