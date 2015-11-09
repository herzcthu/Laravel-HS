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
    
    public function sortQuestions($project, Request $request) {
        

        if ($request->has('listid')) {
                $i = 1;
                foreach ($request->get('listid') as $qid) {
                    $search['id'] = $qid;
                    $data['sort'] = $i;
                    $question = \App\Question::updateOrCreate($search, $data);
                    $i++;
                }
                
                return response()->json(array('success' => $question));
        } else {
                return response()->json(array('success' => false));
        }

		


		
    }
    
    public function updateTranslation(Request $request) {
        $lang_id = $request->get('lang_id');//return $lang_id; die();
            
		foreach($lang_id as $id => $translation){//return $id; die();
                    $translate = Translation::find($id);//return $translate;die();
                    if(!is_null($translate)){
                        //this is original or translated
                        if(!is_null($translate->translation_id)){
                            //this is translated
                            $original = $translate->original;
                        }else{
                            //this is original
                            $original = $translate;
                        }
                        //return $original; die();
                        //$original->translated()->delete();
                        foreach($translation as $lang => $string){
                            $locale = \App\Locale::where('code', $lang)->first();
                            $child = Translation::firstOrNew(['locale_id' => $locale->id, 'translation_id' => $original->id]);
                            $child->translation = $string;
                            $child->original()->dissociate();                            
                            $child->locale()->dissociate();
                            $child->original()->associate($original);
                            $child->locale()->associate($locale);
                            $child->save();
                        }
                    }else{
                        $json['message'] = 'Something wrong!';
                    }
                    
                    /**
                    if(is_array($translation)){
                        foreach($translation as $lang => $translation){
                            $locale = \App\Locale::where('code', $lang)->first();
                            $child = Translation::firstOrNew(['locale_id' => $locale->id, 'translation_id' => $translate->id]);
                            $child->translation = $translation;
                            $child->original()->dissociate();
                            $child->original()->associate($locale);
                            $child->locale()->associate($locale);
                            $child->save();
                        }
                    }else{
			
			$translate->translation = $translation;
			$translate->update();
                    }
                     * 
                     */
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
    public function getResponse($project, Request $request) {
        $total_forms = PLocation::where('org_id', $project->org_id)->count();
        $total_results = Result::where('project_id', $project->id)->count();
        $locations = PLocation::where('org_id', $project->org_id)->groupBy('state')->get();
        
        foreach($locations as $state => $location){
            
            //skip the loop if $location->state is null
            if(is_null($location->state)){
                continue;
            }
            $result[$location->state]['group'] = $location->state;
            $result[$location->state]['region'] = $location->primaryid;
            $state = PLocation::where('org_id', $project->org_id)->where('state', $location->state)->count();
            $statetotal = PLocation::where('org_id', $project->org_id)->where('state', $location->state)->has('results')->count();
            foreach($project->sections as $section => $section_value){
                
                $result[$location->state][$section]['complete'] = PLocation::where('org_id', $project->org_id)
                        ->where('state', $location->state)
                        ->ofWithAndWhereHas('results', function($query) use($section){
                            $query->where('section_id', $section)->where('information', 'complete');
                        })->count();
                $result[$location->state][$section]['incomplete'] = PLocation::where('org_id', $project->org_id)
                        ->where('state', $location->state)
                        ->ofWithAndWhereHas('results', function($query) use($section){
                            $query->where('section_id', $section)->where('information', 'incomplete');
                        })->count();
                $result[$location->state][$section]['error'] = PLocation::where('org_id', $project->org_id)
                        ->where('state', $location->state)
                        ->ofWithAndWhereHas('results', function($query) use($section){
                            $query->where('section_id', $section)->where('information', 'error');
                        })->count();   
                        
                ${$location->state}{$section} = PLocation::where('org_id', $project->id)
                        ->where('state', $location->state)
                        ->ofWithAndWhereHas('results', function($query) use($section){
                            $query->where('section_id', $section);
                        })->count();
                $result[$location->state][$section]['missing'] = $state - ${$location->state}{$section};
            }
            $result[$location->state]['totalmissing'] = $state - $statetotal;
        }
        $result['total']['group'] = 'total';
        $result['total']['region'] = 'all';
        foreach($project->sections as $section => $section_value){
                
                $total_results_by_section = Result::where('project_id', $project->id)->where('section_id', $section)->count();
                
                $result['total'][$section]['complete'] = Result::where('project_id', $project->id)->where('section_id', $section)->where('information', 'complete')->count();
                $result['total'][$section]['incomplete'] = Result::where('project_id', $project->id)->where('section_id', $section)->where('information', 'incomplete')->count();
                $result['total'][$section]['error'] = Result::where('project_id', $project->id)->where('section_id', $section)->where('information', 'error')->count();
                $result['total'][$section]['missing'] = $total_forms - $total_results_by_section;
        } 
        
        $result['total']['totalmissing'] = $total_forms - $total_results;
        //dd(collect($result));
        return Datatables::of(collect($result))->make(true);
    }
    
    public function getStatusCount($project, Request $request) {
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
    
    public function getAllResults($project, Request $request) {
        $result = Result::with('resultable')->with('answers')->with('answers.question.qanswers');
        
        
        /**
        $start = $request->get('start');
        $length = $request->get('length');
        if($request->get('code')){
            $search_key = $request->get('code');
            
            $result = Result::where('project_id', $project->id)->OfWithPcode('pcode', $search_key)->with('resultable')->with('answers')->with('answers.question.qanswers')->orderBy('resultable_id', 'asc')->get();
        
        }elseif($request->get('region')){
            $search_key = $request->get('region');
            
            $result = Result::where('project_id', $project->id)->OfWithPcode('state', $search_key)->with('resultable')->with('answers')->with('answers.question.qanswers')->orderBy('resultable_id', 'asc')->get();
        
        }elseif($request->get('township')){
            $search_key = $request->get('township');
            $result = Result::where('project_id', $project->id)->OfWithPcode('township', $search_key)->with('resultable')->with('answers')->with('answers.question.qanswers')->orderBy('resultable_id', 'asc')->get();
        
        }elseif($request->get('station')){
            $search_key = $request->get('station');
        
            $result = Result::where('project_id', $project->id)->OfWithPcode('village', $search_key)->with('resultable')->with('answers')->with('answers.question.qanswers')->orderBy('resultable_id', 'asc')->get();
        
        
        }elseif($request->get('question') && $request->get('answer')){
            $q = $request->get('question');
            $search_key = $request->get('answer');
            $result = Result::where('project_id', $project->id)
                        ->ofWithAndWhereHas('answers', function($query) use ($q, $search_key){
                            $query->where('qid', $q)->where('akey', $search_key);
                        })
                        ->with('resultable')
                        ->with('answers.question.qanswers')->orderBy('resultable_id', 'asc')
                        ->get();
        }else{
            
        }
        //dd($search_key);
        if(isset($search_key)){
            $result = $result;
        }else{
                        
            $result = Result::where('project_id', $project->id)->with('resultable')->with('answers')->with('answers.question.qanswers')->orderBy('resultable_id', 'asc')->get();
        
        }
        
         * 
         */
        return Datatables::of($result)
                ->filter(function($query) use ($request, $project){
                    if($request->get('pcode')){
                        $code = $request->get('pcode');
                        $query->OfWithPcode('pcode', $code);
                    }
                    if($request->get('region')){
                        $region = $request->get('region');
                        $query->OfWithPcode('state', $region);
                    }
                    if($request->get('district')){
                        $district = $request->get('district');
                        $query->OfWithPcode('district', $district);
                    }
                    if($request->get('township')){
                        $township = $request->get('township');
                        $query->OfWithPcode('township', $township);
                    }
                    if($request->get('vtract')){
                        $village_tract = $request->get('village_tract');
                        $query->OfWithPcode('village_tract', $village_tract);
                    }
                    if($request->get('village')){
                        $village = $request->get('village');
                        $query->OfWithPcode('village', $village);
                    }
                    if( $request->get('question') && $request->get('answer') ){
                        $question = $request->get('question');
                        $answer = $request->get('answer');
                        $query->ofWithAndWhereHas('answers', function($q) use ($question, $answer){
                            $q->where('qid', $question)->where('akey', $answer);
                        })->with('answers');
                    }
                    if($request->get('phone')){
                        $phone = $request->get('phone');
                        if($project->validate == 'pcode'){
                        $query->OfWithParticipant($phone);
                        }
                    }
                    $query->where('project_id', $project->id)->orderBy('resultable_id', 'asc');
                })
                ->editColumn('pcode', function ($model) use ($project){
                if($model->resultable_type == 'App\\PLocation'){
                    return $model->resultable->pcode."<a href='".route('data.project.results.edit', [$project->id, $model->id])."' title='Edit'> <i class='fa fa-edit'></i></a>";
                    }
                })
                ->editColumn('cq', function ($model) use ($project){
                    $q = \App\Question::find($model->section_id);
                    if(!is_null($q)){
                        return $q->question;
                    }else{
                        return 'none';
                    }
                })
                ->editColumn('observers', function ($model) use ($project) {
                    $p = '<table class="table">';
                    if($project->validate == 'pcode'){
                        foreach($model->resultable->participants as $participant){
                            $p .= '<tr><td>'.$participant->name.'</td>';
                            $p .= '<td>';
                            if(isset($participant->phones->mobile)){
                            $p .=  ' M:'.$participant->phones->mobile.'<br>';                            
                            }
                            if(isset($participant->phones->emergency) && $participant->phones->emergency){
                            $p .=  ' E:'.$participant->phones->emergency.'<br>';                            
                            }
                            $p .= '</td></tr>';
                        }
                    $p .= '</table>';    
                    }
                    return $p;
                })
                ->make(true);
    }
    
    public function getAllStatus($project, Request $request){
        $located = PLocation::where('org_id', $project->organization->id )
                ->with('participants')
                
                ->with('answers');
        
        
        
        //$locations = $located->get();
        /**
        if(isset($filter)){
            
        $locations = $located->get();
        }else{
        $locations = PLocation::where('org_id', $project->organization->id )->OfwithAndWhereHas('results', function($query) use ($project){
                        $query->where('project_id', $project->id);

                })->orNotWithResults()->with('participants')->get();
        }
         * 
         */
        
        $datatable = Datatables::of($located)
                ->filter(function($query) use ($request, $project){
                    if($request->get('pcode')){
                        $code = $request->get('pcode');
                        $query->where('pcode',$code);
                        $filter = true;
                    }

                    if($request->get('region')){
                        $state = $request->get('region');
                        if($state != 'total'){
                        $query->where('state',$state);
                        }
                        $filter = true;
                    }
                    if($request->get('township')){
                        $township = $request->get('township');
                        $query->where('township',$township);
                        $filter = true;
                    }
                    if($request->get('station')){
                        $station = $request->get('station');
                        $query->where('village',$station);
                        $filter = true;
                    }
                    if($request->get('phone')){
                        $phone = $request->get('phone');
                        $query->OfWithAndWhereHas('participants',function($query) use ($phone){
                            $query->where('phones', 'like','%'.$phone.'%');
                        });
                        $filter = true;
                    }                    

                    if(!is_null($request->get('section')) && $request->get('section') >= 0){ 
                        $section = $request->get('section');
                        $status = $request->get('status');
                        if($status == 'missing'){
                            $query->whereDoesntHave('results', function($query) use ($project, $section){
                                $query->where('project_id', $project->id)->where('section_id', $section);
                            });
                        }else{

                            $query->OfwithAndWhereHas('results', function($query) use ($project, $section, $status){
                                    $query->where('project_id', $project->id)->where('information', $status)->where('section_id', (int)$section);
                            })->with('results');
                        }

                        $filter = true;
                    }
                    
                    $query->OfwithAndWhereHas('results', function($query) use ($project){
                        $query->where('project_id', $project->id);});
                })
                ->editColumn('pcode', function ($model) use ($project){
                    //if($model->results){
                    return $model->pcode."<a href='".route('data.project.results.edit', [$project->id, $model->primaryid])."' title='Edit'> <i class='fa fa-edit'></i></a>";
                    //}
                })
                ->editColumn('state', function ($model) use ($project){
                    $state = (!is_null($model->state))? $model->state:'';
                    return _t($state);
                })
                ->editColumn('township', function ($model) use ($project){
                    $township = (!is_null($model->township))? $model->township:'';
                    return _t($township);
                })
                ->editColumn('village', function ($model) use ($project){
                    $village = (!is_null($model->village))? $model->village:'';
                    return _t($village);
                })
                ->editColumn('observers', function ($model) use ($project) {
                    $p = '<table class="table">';
                    if($project->validate == 'pcode'){
                        foreach($model->participants as $participant){
                            $p .= '<tr><td>'.$participant->name.'</td>';
                            $p .= '<td>';
                            if(isset($participant->phones->mobile)){
                            $p .=  ' M:'.$participant->phones->mobile.'<br>';                            
                            }
                            if(isset($participant->phones->emergency) && $participant->phones->emergency){
                            $p .=  ' E:'.$participant->phones->emergency.'<br>';                            
                            }
                            $p .= '</td></tr>';
                        }
                    $p .= '</table>';    
                    }
                    return $p;
                })
                ->make(true);
        return $datatable; 
    }
    
    public function getAllStatusBK($project, Request $request)
    { //dd($request->all());
        $start = $request->get('start');
        $length = $request->get('length');
        if($request->get('code')){
            $pcode = $request->get('code');
            $located = PLocation::where('org_id', $project->organization->id )
                    ->where('pcode',$pcode)
                    ->orNotWithResults()
                    ->OfWithAndWhereHas('results',function($query) use ($project){
                        $query->where('project_id', $project->id);
                    })
                    ->with('participants')
                    ->get();
        
        }elseif($request->get('region')){
            $state = $request->get('region');
            
            $located = PLocation::where('org_id', $project->organization->id )
                    ->where('state',$state)
                    ->orNotWithResults()
                    ->OfWithAndWhereHas('results',function($query) use ($project){
                        $query->where('project_id', $project->id);
                    })
                    ->with('participants')->get();
        }elseif($request->get('district')){
            $district = $request->get('district');
            $located = PLocation::where('org_id', $project->organization->id )
                    ->where('district',$district)
                    ->orNotWithResults()
                    ->OfWithAndWhereHas('results',function($query) use ($project){
                        $query->where('project_id', $project->id);
                    })
                    ->with('participants')->get();
        }elseif($request->get('station')){
            $search_key = $request->get('station');
            $located = PLocation::where('org_id', $project->organization->id )
                    ->where('village',$search_key)
                    ->orNotWithResults()
                    ->OfWithAndWhereHas('results',function($query) use ($project){
                        $query->where('project_id', $project->id);
                    })
                    ->orderBy('village')
                    ->with('participants')->get();
        }elseif($request->get('section') >= 0){
            $section = $request->get('section');
            $search_key = $request->get('status');
            if($search_key == 'missing'){

                $located = PLocation::where('org_id', $project->organization->id )->OfwithAndWhereHas('results', function($query) use ($project, $section, $search_key){
                        $query->where('project_id', $project->id)->where('section_id', (int)$section)
                                ->whereNotIn('information',['complete', 'incomplete', 'error']);

                })->orNotWithResults()->with('participants')->get();
            }else{
                $located = PLocation::where('org_id', $project->organization->id )->OfwithAndWhereHas('results', function($query) use ($project, $section, $search_key){
                        $query->where('project_id', $project->id)->where('information', $search_key)->where('section_id', (int)$section);

                })->with('participants')->with('answers')->get();
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
            $locations = PLocation::where('org_id', $project->organization->id )->OfwithAndWhereHas('results', function($query) use ($project){
                        $query->where('project_id', $project->id);

                })->orNotWithResults()->with('participants')->get();
        }
        
        $datatable = Datatables::of($locations)
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
        return $datatable;
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
                    if(!is_null($sM->supervisor)){
                       // $response[$sM->supervisor->role->name] = $sM->supervisor->name;
                    }
                    //$response[$sM->role->name.' '.$sM_id ] = $sM->name;
                    //}
                   // $response[$sM->role->name.' '.$sM_id.' ID'] = $sM->participant_id;
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
