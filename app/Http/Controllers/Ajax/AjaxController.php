<?php namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Location;
use App\Participant;
use App\PLocation;
use App\Repositories\Backend\Location\LocationContract;
use App\Repositories\Backend\Participant\ParticipantContract;
use App\Repositories\Backend\Participant\Role\RoleRepositoryContract;
use App\Repositories\Backend\PLocation\PLocationContract;
use App\Repositories\Frontend\Result\ResultContract;
use App\Result;
use App\Translation;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use yajra\Datatables\Facades\Datatables;

class AjaxController extends Controller
{
    protected $locations;
    
    protected $country;
    
    protected $plocation;
    
    protected $participant;
    
    protected $proles;
    
    protected $results;


    public function __construct(
            PLocationContract $plocation, 
            ParticipantContract $participant,
            LocationContract $locations,
            RoleRepositoryContract $proles,
            ResultContract $results) {
        $this->plocation = $plocation;
        $this->participant = $participant;
        $this->locations = $locations;
        $this->country = config('aio.country');
        $this->proles = $proles;
        $this->results = $results;
    }
    
    public function updateTranslation(Request $request) {
        $lang_id = $request->get('lang_id');
		foreach($lang_id as $id => $translation){
			$locale_id = Translation::where('translation_id', '=', $id)->pluck('id');
			$locale = Translation::find($locale_id);
			$locale->translation_id = $id;
			$locale->translation = $translation;
			$locale->update();
		}
		$json['status'] = true;
		$json['message'] = 'Translation updated!';
		return json_encode($json);
    }
    
    public function timeGraph($project, Request $request){
        $last = Result::where('project_id', $project->id)->orderBy('created_at', 'desc')->first();
        if(!$last) return;
        $last_time = $last->created_at;
        $last_time = $last_time->subDay();
        
        foreach($project->sections as $section => $section_value){
            $query['p'.$project->id.'s'.$section] = DB::table('results')
                ->select(DB::raw('count(*) as resultcount'),DB::raw('ROUND(UNIX_TIMESTAMP(created_at)/(5 * 60)) AS timekey'), DB::raw('UNIX_TIMESTAMP(created_at) as created'))
                ->groupBy('timekey')->where('project_id', $project->id)->where('section_id',$section)->get();
            //$query['p'.$project->id.'s'.$section]['label'] = $section_value->text;
        }
        foreach($query as $qk => $qv){
            foreach($qv as $k=>$v){
                $result[$qk][$k]['y'] = $v->resultcount;
                $result[$qk][$k]['x'] = $v->created * 1000;
            }
        }
        $result['last'] = $last_time->timestamp * 1000;
        return response()->json($result);
    }
    
    public function getStatus($project, Request $request) {
        //$section = $request->get('section');
        $location = $request->get('location');
        $loctype = $request->get('loctype');
        foreach($project->sections as $section => $section_value){
            if($loctype && $location){
                $total_forms = PLocation::where('org_id', $project->org_id)->where($loctype, $location)->count(); //dd($total_forms);
                $total_results = Result::where('project_id', $project->id)->where('section_id', $section)->OfWithPcode($loctype,$location)->count();
                $result['complete'][$section]['y'] = Result::where('project_id', $project->id)->where('section_id', $section)->where('information', 'complete')->OfWithPcode($loctype,$location)->count();
                $result['incomplete'][$section]['y'] = Result::where('project_id', $project->id)->where('section_id', $section)->where('information', 'incomplete')->OfWithPcode($loctype,$location)->count();
                $result['error'][$section]['y'] = Result::where('project_id', $project->id)->where('section_id', $section)->where('information', 'error')->OfWithPcode($loctype,$location)->count();
                $result['missing'][$section]['y'] = $total_forms - $total_results;
                $result['complete'][$section]['label'] = $section_value->text;
                $result['incomplete'][$section]['label'] = $section_value->text;
                $result['error'][$section]['label'] = $section_value->text;
                $result['missing'][$section]['label'] = $section_value->text;
                

            }else{
                $total_forms = PLocation::where('org_id', $project->org_id)->count();
                $total_results = Result::where('project_id', $project->id)->where('section_id', $section)->count();
                $result['complete'][$section]['y'] = Result::where('project_id', $project->id)->where('section_id', $section)->where('information', 'complete')->count();
                $result['incomplete'][$section]['y'] = Result::where('project_id', $project->id)->where('section_id', $section)->where('information', 'incomplete')->count();
                $result['error'][$section]['y'] = Result::where('project_id', $project->id)->where('section_id', $section)->where('information', 'error')->count();
                $result['missing'][$section]['y'] = $total_forms - $total_results;
                $result['complete'][$section]['label'] = _t($section_value->text);
                $result['incomplete'][$section]['label'] = _t($section_value->text);
                $result['error'][$section]['label'] = _t($section_value->text);
                $result['missing'][$section]['label'] = _t($section_value->text);
                //$result['complete'][$section]['indexLabel'] = _t($section_value->text);
                //$result['incomplete'][$section]['indexLabel'] = _t($section_value->text);
                //$result['error'][$section]['indexLabel'] = _t($section_value->text);
                //$result['missing'][$section]['indexLabel'] = _t($section_value->text);
                //$result['complete'][$section]['indexLabelPlacement'] = 'inside';
                //$result['incomplete'][$section]['indexLabelPlacement'] = 'inside';
                //$result['error'][$section]['indexLabelPlacement'] = 'inside';
                //$result['missing'][$section]['indexLabelPlacement'] = 'inside';

            }
        }
    //$result = Result::where('section_id', $section)->where('information', $status)->OfWithPcode('state','Yangon')->count();
        return response()->json($result);
    }
    
    public function getAllResultsBK($project, Request $request) {
        $results = Result::where('project_id', $project->id )->with('resultable')->get();
        return Datatables::of($results)
                ->editColumn('locations',function($model){
                    if($model->resultable instanceof PLocation){
                        return $model->resultable;
                    }
                })
                ->editColumn('observers', function($model){
                    $p = '';
                    if($model->resultable instanceof PLocation){
                        foreach($model->resultable->participants as $participant){
                           //dd($participant);
                            $p .= $participant->name.'('.$participant->participant_id.')<br>';
                        }
                        return $p;
                    }
                    if($model->resultable instanceof Participant){
                        return 'participant';
                    }
                })
                ->make(true);
    }
    
    public function getAllResults($project, Request $request)
    { //dd($request->all());
        if($request->get('code')){
            $search_key = $request->get('code');
            $located = PLocation::where('org_id', $project->organization->id )->where('pcode',$search_key)->with('results')->with('participants')->get();
        
        }elseif($request->get('region')){
            $search_key = $request->get('region');
            
            $located = PLocation::where('org_id', $project->organization->id )->where('state',$search_key)->with('results')->with('participants')->get();
        }elseif($request->get('district')){
            $search_key = $request->get('district');
            $located = PLocation::where('org_id', $project->organization->id )->where('district',$search_key)->with('results')->with('participants')->get();
        }elseif($request->get('station')){
            $search_key = $request->get('station');
            $located = PLocation::where('org_id', $project->organization->id )->where('village',$search_key)->with('results')->with('participants')->get();
        }elseif($request->get('section') >= 0){
            $section = $request->get('section');
            $search_key = $request->get('status');
            if($search_key == 'missing'){

                $located = PLocation::where('org_id', $project->organization->id )->OfwithAndWhereHas('results', function($query) use ($section, $search_key){
                        $query->where('section_id', (int)$section)
                                ->whereNotIn('information',['complete', 'incomplete', 'error']);

                })->orNotWithResults()->with('results')->with('participants')->get();
            }else{
                $located = PLocation::where('org_id', $project->organization->id )->OfwithAndWhereHas('results', function($query) use ($section, $search_key){
                        $query->where('information', $search_key)->where('section_id', (int)$section);

                })->with('results')->with('participants')->with('answers')->get();
            }
        }else{
            
        }
        //dd($search_key);
        if(isset($search_key)){
            $locations = $located;
            $sections = $project->sections;
        }else{
            $results = $project->results;
            $sections = $project->sections;
            $locations = PLocation::where('org_id', $project->organization->id )->with('results')->with('participants')->get();
        }
        
        //dd($locations);
        return Datatables::of($locations)
                ->editColumn('code', function ($model) use ($project){
                    //if($model->results){
                    return $model->pcode."<a href='".route('data.project.results.edit', [$project->id, $model->primaryid])."' title='Edit'> <i class='fa fa-edit'></i></a>";
                    //}
                })
                ->editColumn('state', function ($model) use ($project){
                    $state = (!is_null($model->state))? $model->state:'';
                    return _t($state);
                })
                ->editColumn('district', function ($model) use ($project){
                    $district = (!is_null($model->district))? $model->district:'';
                    return _t($district);
                })
                ->editColumn('village', function ($model) use ($project){
                    $village = (!is_null($model->village))? $model->village:'';
                    return _t($village);
                })
                ->editColumn('observers', function ($model) {
                    $p = '';
                    foreach($model->participants as $participant){
                        $p .= $participant->name.'('.$participant->participant_id.') <br>';
                    }
                    return $p;
                })
                ->make(true);
    }
    
    public function formValidatePerson($project, $person, Request $request) {
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
                    //if($sM->name != 'No Name'){
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
    
    /**
     * Ajax search method for all locations
     */
    public function searchLocationsOnlyName(Request $request) {
        $term = $request->get('term');
        if (strlen($term) != strlen(utf8_decode($term))) {
        // $str uses multi-byte chars (isn't English)
            $column = 'mya_name';
        } else {
            // $str is ASCII (probably English)
            $column = 'name';
        }
        $order_by = $column;
        $result = $this->locations->searchOnlyName($term, $this->country, $column)->orderBy($order_by, 'asc')->get();
        $result = $result->transform(function ($item, $key) use ($column) {
                    $item['value'] = $item[$column];
                    $item['label'] = $item[$column];
                    return $item;
                });
        return response()->json($result);
    }
    /**
     * Ajax response for states
     */
    public function allstates(){
        return response()->json($this->locations->getStatesScope($this->country, 'name', 'asc')->lists('name','id'));
    }
    
    /**
     * Ajax response for states
     */
    public function alldistricts(){
        return response()->json($this->locations->getDistrictsScope($this->country, 'name', 'asc')->lists('name','id'));
    }
    
    /**
     * Ajax response for states
     */
    public function alltownships(){
        return response()->json($this->locations->getTownshipsScope($this->country, 'name', 'asc')->lists('name','id'));
    }
    
    /**
     * Ajax response for states
     */
    public function allvillagetracks(){
        return response()->json($this->locations->getVtracksScope($this->country, 'name', 'asc')->lists('name','id'));
    }
    
    /**
     * Ajax response for states
     */
    public function allvillages(){
        return response()->json($this->locations->getVillagesScope($this->country, 'name', 'asc')->lists('name','id'));
    }
    
    /**
     * Ajax response villages from $id
     */
    public function villages_by_id($id) {
        $location = $this->locations->findVillagesById($id, 'name', 'asc');
        
        if($location instanceof Location){
            return response()->json([$location]);
        }
        if($location instanceof Builder){
            return response()->json($location->get());
        }
        
    }
    
    /**
     * Ajax response villagetracks from $id
     */
    public function villagetracks_by_id($id) {
        $location = $this->locations->findVTracksById($id, 'name', 'asc');
        
        if($location instanceof Location){
            return response()->json([$location]);
        }
        if($location instanceof Builder){
            return response()->json($location->get());
        }        
    }
    
    /**
     * Ajax response townships from $id
     */
    public function townships_by_id($id) {
        $location = $this->locations->findTownshipsById($id, 'name', 'asc');
        
        if($location instanceof Location){
            return response()->json([$location]);
        }
        if($location instanceof Builder){
            return response()->json($location->get());
        }        
    }
    
    /**
     * Ajax response districts from $id
     */
    public function districts_by_id($id) {
        $location = $this->locations->findDistrictsById($id, 'name', 'asc');
        
        if($location instanceof Location){
            return response()->json([$location]);
        }
        if($location instanceof Builder){
            return response()->json($location->get());
        }        
    }
    
    /**
     * Ajax response townships from $id
     */
    public function states_by_id($id) {
        $location = $this->locations->findStatesById($id, 'name', 'asc');
        
        if($location instanceof Location){
            return response()->json([$location]);
        }
        if($location instanceof Builder){
            return response()->json($location->get());
        }        
    }
}
