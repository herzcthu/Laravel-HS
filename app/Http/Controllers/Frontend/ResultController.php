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
        
        $sections = $project->sections;
        $alocations = PLocation::where('org_id', $project->organization->id )->get();
        return view('frontend.result.index-locations')
                        ->withParticipants($this->participants)
                        ->withProject($project)
                        ->withSections($sections)
                        //->withLocations($locations)
                        ->withAllLoc($alocations)
                        ->withRequest($request);
    }
    
    public function surveyIndex($project, Request $request) {
        $alocations = PLocation::where('org_id', $project->organization->id )->get();
        return view('frontend.result.survey-index')
                    ->withAllLoc($alocations)
                    ->withProject($project)
                    ->withRequest($request);
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
            $route = route('ajax.project.pcode', [$project->id, '{pcode}']);
        }elseif($project->validate == 'uec_code'){
            $route = '';
        }else{
            $route = '';
        }
        $translate = route('ajax.translate');
        javascript()->put([
			'url' => $route,
                        'translateurl' => $translate
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
    public function store($project, CreateResultRequest $request)
    { //dd($request->all());
        
        $result = $this->results->create(
			$request->except('project_id'),
			$project
		);
        
        
        if($project->type == 'incident'){
            return redirect()->route('data.project.results.edit', [$project->id, $result->id])->withFlashSuccess('The results was successfully created.');
        }else{
            return redirect()->back()->withFlashSuccess('The results was successfully created.');
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
            $route = route('ajax.project.pcode', [$project->id, '{pcode}']);
        }elseif($project->validate == 'uec_code'){
            $route = '';
        }else{
            $route = '';
        }
        
        
        if($code instanceof \App\Result){
            $validated = $this->formValidatePcode($project, $code->resultable, $request);
            if($code->resultable_type == 'App\PLocation') {
            $idcode = $code->resultable->pcode;
            }
            if($code->resultable_type == 'App\Participant') {
            $idcode = $code->resultable->participant_code;
            }
            $validated['validator_key'] = $code->id;
            $validated['validator'] = $idcode;
        }
        $translate = route('ajax.translate');
        javascript()->put([
			'url' => $route,
                        'translateurl' => $translate
		]); 
        
        return view('frontend.result.edit')
			->withUser($user)
                        ->withProject($project)
                        ->withValidated($validated)
                        ->withResult($code);
    }
    
    public function editSurvey($project, $code, $form, Request $request) {
        $user = auth()->user();
        if($project->validate == 'person'){
            $route = route('ajax.project.person', [$project->id, '{pcode}']);
        }elseif($project->validate == 'pcode'){
            $route = route('ajax.project.pcode', [$project->id, '{pcode}']);
        }else{
            $route = '';
        }
        
        $results = $code->results->where('project_id', $project->id)->where('incident_id', (int)$form);
        
        if(empty($results->all())) {
            return redirect()->route('data.project.survey.index',[$project->id])->withFlashWarning('Data not exist.');
        }
        $translate = route('ajax.translate');
        javascript()->put([
			'url' => $route,
                        'translateurl' => $translate
		]); 
        return view('frontend.result.edit-survey')
			->withUser($user) // App\User
                        ->withProject($project) // App\Project
                        ->withCode($code) //App\PLocation
                        ->withResults($results)
                        ->withForm($form); // incident_id
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update($project, $code, CreateResultRequest $request)
    {               
        $form_id = $request->only('form_id')['form_id'];
        $result = $this->results->update(
                        $code,
			$request->except('project_id','form_id'),
			$project,
                        $form_id
		);
        //dd($result);
        if($project->type == 'incident' || $project->type == 'survey'){
            return redirect()->route('data.project.code.form.edit', [$project->id, $code->id, $form_id])->withFlashSuccess('The results was successfully created.');
        }else{
            return redirect()->back()->withFlashSuccess('The results was successfully created.');
            //return redirect()->route('data.project.status.index', $project->id)->withFlashSuccess('The results was successfully created.');
        }
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
                    ->where('org_id', $project->organization->id);
            }elseif($key == 'District'){
                $located = $this->plocation->getDistrictsScope($pcode->district, 'village')
                    ->where('org_id', $project->organization->id);
            }elseif($key == 'Township'){
                $located = $this->plocation->getTownshipsScope($pcode->township, 'village')
                    ->where('org_id', $project->organization->id);
            }elseif($key == 'VTract'){
                $located = $this->plocation->getVTractsScope($pcode->village_tract, 'village')
                    ->where('org_id', $project->organization->id);
            }else{
                $located = $this->plocation->getVillagesScope($pcode->village, 'village')
                    ->where('org_id', $project->organization->id);
            }
            
            if(!$located->get()->isEmpty()){
                $locatedMembers = $located->first()->participants;
                //dd($village_tractMembers->count());
                foreach ($locatedMembers as $key => $sM){
                    $sM_id = str_replace($pcode->pcode, '', $sM->participant_id);
                    if(!is_null($sM->supervisor)){
                        //$response[$sM->supervisor->role->name] = $sM->supervisor->name;
                    }
                    //$response[$sM->role->name.' '.$sM_id ] = $sM->name;
                    //}
                    //$response[$sM->role->name.' '.$sM_id.' ID'] = $sM->participant_id;
                }
            }
        }
        if(!is_null($pcode->participants)){
            $first = $pcode->participants->first();
            
            if(!is_null($first->supervisor)){
                if(!empty($first->supervisor->name)){
                $response[$first->supervisor->role->name] = $first->supervisor->name;  
                }
            }
            
            foreach($pcode->participants as $participant){
                $response[$participant->role->name.' '.str_replace($pcode->pcode, '', $participant->participant_id)] = $participant->name;
                $response[$participant->role->name.' '.str_replace($pcode->pcode, '', $participant->participant_id).' ID'] = $participant->participant_id;
                foreach($participant->phones as $pk => $phone){
                    if(!empty($phone)){
                    $response[$participant->role->name.' '.str_replace($pcode->pcode, '', $participant->participant_id).' '.$pk] = $phone;
                    }
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
