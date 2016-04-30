<?php namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Locale;
use App\PLocation;
use App\Project;
use App\Question;
use App\Repositories\Backend\Organization\OrganizationContract;
use App\Repositories\Backend\Participant\ParticipantContract;
use App\Repositories\Backend\Participant\Role\RoleRepositoryContract;
use App\Repositories\Backend\PLocation\PLocationContract;
use App\Repositories\Backend\Question\QuestionContract;
use App\Repositories\Frontend\Result\ResultContract;
use App\Result;
use App\Translation;
use DB;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\Datatables\Facades\Datatables;

class AjaxController extends Controller
{
    protected $country;
    
    protected $plocation;
    
    protected $participant;
    
    protected $proles;
    
    protected $results;
    
    protected $question;
    
    protected $organizations;


    public function __construct(
            PLocationContract $plocation, 
            ParticipantContract $participant,
            RoleRepositoryContract $proles,
            ResultContract $results,
            QuestionContract $question,
            OrganizationContract $organizations) {
        $this->plocation = $plocation;
        $this->participant = $participant;
        $this->country = config('aio.country');
        $this->proles = $proles;
        $this->results = $results;
        $this->question = $question;
        $this->organizations = $organizations;
    }
    
    public function sortQuestions($project, Request $request) {
        

        if ($request->has('listid')) {
                $i = 1;
                foreach ($request->get('listid') as $qid) {
                    $search['id'] = $qid;
                    $data['sort'] = $i;
                    $question = Question::updateOrCreate($search, $data);
                    $i++;
                }
                
                return response()->json(array('success' => $question));
        } else {
                return response()->json(array('success' => false));
        }

		


		
    }
    
    public function newQuestion($project, Request $request){
        if($request->ajax()){
            $ajax = true;
        }
        $input = $request->all();
        // get urlhash from request header
        $urlhash = $request->header('X-URLHASH');
        if(empty($urlhash)) {
            return response()->json(array('success'=>false));
        }else{
            if (Hash::check($request->url(), $urlhash)) {
                // url match with hash...
                $question = $this->question->create($input, $project, $ajax);
                
                return response()->json(array('success'=>true, 'message'=>$question));
            } else {
                return response()->json(array('success'=>false));
            }
        }        
        
    }
    
    public function editQuestion($project, $question, Request $request){
        if($request->ajax()){
            $ajax = true;
        }
        if($request->has('logicdata')){
            $logic = $question->logic;
            $logic[$request->get('lftans')] = $request->except('_method','logicdata');
            $input['logic'] = $logic;
        }else{
            $input = $request->all();
        }
        // get urlhash from request header
        $request_urlhash = $request->header('X-URLHASH');
        if(empty($request_urlhash)) {
            return response()->json(array('success'=>false, 'message' => $urlhash));
        }else{
            if (Hash::check($request->url(),$request_urlhash)) {
                // url match with hash...
                $question = $this->question->update($question, $input, $project, $ajax);
                return $question;
            } else {
                return response()->json(array('success'=>false, 'reqhash' => $request_urlhash, 'requesturl' => $request->url()));
            }
        }
    }
    
    public function addLogic($project,$question, Request $request) {
        if($request->ajax()){
            $ajax = true;
        }
        if($request->has('logicdata')){
            //$logic = $question->logic;
            $input = $request->except('_method','_hash','logicdata');
        } else {
            return response()->json(array('success'=>false));           
        }
        // get urlhash from request header
        $request_urlhash = $request->get('_hash');
        if(empty($request_urlhash)) {
            return response()->json(array('success'=>false, 'message' => $urlhash));
        }else{
            if (Hash::check($request->url(),$request_urlhash)) {
                //return $input;
                // url match with hash...
                $question = $this->question->addLogic($project, $question, $input, $ajax);
                return $question;
            } else {
                return response()->json(array('success'=>false, 'reqhash' => $request_urlhash));
            }
        }
    }
    
    /**
     * 
     * @param type $project
     */
    public function getProject($project, Request $request){
        
    }
    
    /**
     * Get all questions in a project
     * @param type $project
     * @param array $column
     */
    public function getQuestions($project, Request $request){
        if($request->get('columns')){
            $columns = $request->get('columns');
            //$key = $columns['key'];
            //$value = $columns['value'];
            //$columns = ['id','qnum','question'];
            $questions = $project->questions->map(function ($item, $key) use ($columns) {
                return array_intersect_key($item->toArray(), array_flip ($columns));
            });
        }else{
            $questions = $project->questions;
        }
        
        return json_encode($questions);
    }
    
    public function getQuestion($project, $question, Request $request) {
        // Still don't sure why should I query like this
        //dd($question->where('id','=',$question->id)->get());
        if($request->get('columns')){
            $columns = $request->get('columns');
            $q = $question->where('id','=',$question->id)->get()->map(function ($item, $key) use ($columns) {
                return array_intersect_key($item->toArray(), array_flip ($columns));
            });
        }else{
            $q = $question;
        }
      
        return json_encode($q);
    }
    
    /**
     * Get all answers in a question
     * @param type $project
     * @param type $question
     * @param array $column
     */
    public function getAnswers($project, $question, Request $request){
        if($request->get('columns')){
            $columns = $request->get('columns');
            //$key = $columns['key'];
            //$value = $columns['value'];
            $answers = $question->qanswers->map(function ($item, $key) use ($columns) {
                return array_intersect_key($item->toArray(), array_flip ($columns));
            });
        }else{
            $answers = $question->qanswers;
        }
        
        return json_encode($answers);
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
                            $locale = Locale::where('code', $lang)->first();
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
        /**
         * Get last created data timestamp
         */
        $last = Result::where('project_id', $project->id)->orderBy('created_at', 'desc')->first();
        if(!$last) return;
        $last_time = $last->created_at;
        $last_time = $last_time->subDay();
        
        foreach($project->sections as $section => $section_value){
            /**
             * Group data entry in every 5 minutes
             */
            $query['p'.$project->id.'s'.$section] = DB::table('results')
                ->select(DB::raw('count(*) as resultcount'),DB::raw('ROUND(UNIX_TIMESTAMP(created_at)/(5 * 60)) AS timekey'), DB::raw('UNIX_TIMESTAMP(created_at) as created'))
                ->groupBy('timekey')->where('project_id', $project->id)->where('section_id',$section)->get();
            //$query['p'.$project->id.'s'.$section]['label'] = $section_value->text;
        }
        foreach($query as $qk => $qv){
            foreach($qv as $k=>$v){
                $result[$qk][$k]['y'] = $v->resultcount;
                $result[$qk][$k]['x'] = $v->created * 1000; //Convert to javascript timestamp from mysql timestamp
            }
        }
        $result['last'] = $last_time->timestamp * 1000;
        /**
         * Return json response for time graph in dashboard
         */
        return response()->json($result);
    }
    
    public function getResponse($project , Request $request) {
        $column = DB::select(DB::raw("SELECT "
                . "GROUP_CONCAT(DISTINCT "
                . "CONCAT("
                . "\"SUM(IF(section_id = \",section_id,\" AND information = '\",information,\"', 1, 0)) AS s\", section_id,information "
                . ")"   
                . ") AS sections,  "
                . "GROUP_CONCAT(DISTINCT "
                . "CONCAT("
                . "\"COUNT(DISTINCT pcode.primaryid) - SUM(IF(section_id = \",section_id,\", 1, 0)) AS s\", section_id,\"missing\" "
                . ")) AS sectiontotal "
                . "FROM results WHERE (project_id = $project->id);"));
        //dd($column[0]);
        $project_id = $project->id;
        $org_id = $project->org_id;
        $query = [
            //'pcode.pcode', 
            'pcode.state', 
            'pcode.district', 
            'pcode.township', 
            'pcode.village',
            'pcode.org_id',
            DB::raw('COUNT(DISTINCT pcode.primaryid) AS total')
            ];
        if(!empty($column[0]->sections)){
            $query[] = DB::raw($column[0]->sections);
        }
        if(!empty($column[0]->sectiontotal)){
            $query[] = DB::raw($column[0]->sectiontotal);
        }
        $responses = PLocation::select($query)
                ->where('pcode.org_id', $org_id)
                ->leftjoin('results',function($results) use ($org_id){
                    $results->on('results.resultable_id','=','pcode.primaryid')
                            ->where('pcode.org_id','=', $org_id);
                });
            
            
            $datatables =  Datatables::of($responses);
            if($request->has('area')){
                $area = $request->get('area');
                $datatables->groupBy('pcode.'.$area);
            } else {
                $datatables->groupBy('pcode.org_id');
            }
            $datatables->editColumn('state', function ($modal) use ($project, $request){
                            if($request->has('area')){
                                $area = $request->get('area');
                                switch ($area) {
                                    case 'state':
                                        return $modal->state;
                                        break;
                                    case 'township':
                                        return $modal->township;
                                        break;
                                    default:
                                        return;
                                        break;
                                }
                                
                            } else {
                                return "Total";
                            }
                        });
            if ($township = $request->get('township')) {
                $datatables->where('pcode.township', 'like', "$township%"); // additional pcode.township search
            }        
            return $datatables->make(true);
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
    
    public function getAllResults2($project, Request $request) {
        $query = \DB::select(\DB::raw("SELECT pcode.state,pcode.township,pcode.village,pcode.pcode,"
                . "rs.section_id,rs.results,rs.information,rs.updated_at,users.name "
                . "FROM (SELECT pcode.* FROM pcode WHERE (pcode.org_id = $project->org_id)) AS pcode LEFT JOIN results as rs ON (rs.resultable_id = pcode.primaryid) AND (rs.project_id = $project->id) "
                . ""
                . "ORDER BY pcode.pcode"));
        return Datatables::of($result)
                ->make(true);
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
                    $q = Question::find($model->section_id);
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
    
    /**
     * 
     * @param Project $project
     * @param Request $request
     * @return string
     */
    public function getAllStatus($project, Request $request) {
        
        $parameters = [
            'project_id' => $project->id,
            'org_id' => $project->org_id
        ];
        // get sections as columns for next query
        //$column = DB::select(DB::raw("SELECT GROUP_CONCAT(DISTINCT CONCAT(\"MAX(IF(results.section_id = \",section_id,\", results.information, NULL)) AS s\", section_id)) AS sections  FROM results WHERE (project_id = $project->id);"));
        $column = DB::select(DB::raw("SELECT GROUP_CONCAT(DISTINCT CONCAT('MAX(IF(results.section_id = ',results.section_id,', results.information, NULL)) AS s', results.section_id)) AS sections  FROM results WHERE (results.project_id = $project->id);"));
        //$column = DB::select(DB::raw("SELECT GROUP_CONCAT(DISTINCT CONCAT('MAX(IF(section_id = ',section_id,', information, NULL)) AS s', section_id)) AS sections  FROM results;"));
        
        /**
        $query = "SELECT pcode.pcode, pcode.state, pcode.district, pcode.township, pcode.village,";
        $query .= "GROUP_CONCAT(DISTINCT CONCAT(p.name,\"|\", p.participant_id,\"|\", p.phones) ORDER BY p.name) AS observers,";
        $query .= $column[0]->sections;
        $query .= " FROM (SELECT pcode.* FROM pcode WHERE (pcode.org_id = :org_id)) AS pcode ";
        $query .= "LEFT JOIN participants as p ON p.pcode_id = pcode.primaryid "
                ."LEFT JOIN results as rs ON (rs.resultable_id = pcode.primaryid) "
                ."AND (rs.project_id = :project_id) GROUP BY pcode.pcode;";
        $status = DB::select($query,$parameters);
         * 
         */
        $query = ['pcode.primaryid',
            'pcode.pcode', 
            'pcode.state', 
            'pcode.district', 
            'pcode.township', 
            'pcode.village'];
        //$query[] = DB::raw('GROUP_CONCAT(DISTINCT "\"",p.name,"\":",CONCAT("{\"name\":\"",p.name,"\",\"id\":\"", p.participant_id,"\",\"phones\":", p.phones, "}") ORDER BY p.name) AS observers');
        if(!empty($column[0]->sections)){
            $query[] = DB::raw($column[0]->sections);
        }
        
        $project_id = $project->id;
        $org_id = $project->org_id;
        $status = PLocation::select($query)
                ->where('pcode.org_id', $org_id)
                ->with(['participants'])
                //->with(['participants', 'results' => function($q) use ($project_id){
                //    $column = "SELECT GROUP_CONCAT(DISTINCT CONCAT('MAX(IF(section_id = ',section_id,', information, NULL)) AS s', section_id)) AS sections  FROM results;";
                //    $q->where('project_id','=', $project_id)->addSelect(new \Illuminate\Database\Query\Expression("DB::raw($column)"));
                //}])
                ->leftjoin('results',function($pcode) use ($project_id){
                    $pcode->on('pcode.primaryid','=','results.resultable_id')
                            ->where('results.project_id','=', $project_id);
                })                
                ->groupBy('pcode.primaryid')->get();
                //dd($status);
                return Datatables::of($status)
                        ->filter(function($instance) use ($request){
                            if($request->has('pcode')){
                                $code = $request->get('pcode');
                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                    return Str::contains($row['pcode'], $request->get('pcode')) ? true : false;
                                });
                            }
                            if($request->has('region')){
                                $code = $request->get('region');
                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                    return Str::contains($row['state'], $request->get('region')) ? true : false;
                                });
                            }
                            if($request->has('township')){
                                $code = $request->get('township');
                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                    return Str::contains($row['township'], $request->get('township')) ? true : false;
                                });
                            }
                            if($request->has('station')){
                                $code = $request->get('station');
                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                    return Str::contains($row['village'], $request->get('station')) ? true : false;
                                });
                            }
                            if($request->has('phone')){
                                $code = $request->get('phone');
                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                    //dd($row->toArray());
                                    return Str::contains($row['participants'], $request->get('phone')) ? true : false;
                                });
                            }
                            
                            if(!is_null($request->get('section')) && $request->get('section') >= 0){ 
                                $section = "s".$request->get('section'); // array key will be s0,s1,s2 etc..
                                $status = $request->get('status');
                                if($status == 'missing'){
                                    $instance->collection = $instance->collection->filter(function ($row) use ($request, $section) {
                                        return Str::is($row[$section], null) ? true : false;
                                    });
                                }else{
                                   $instance->collection = $instance->collection->filter(function ($row) use ($request, $section, $status) {
                                        return Str::is($row[$section], $status) ? true : false;
                                    });
                                }
                            }
                        })
                        ->editColumn('pcode', function ($modal) use ($project){
                            //if($modal->results){
                            return $modal->pcode."<a href='".route('data.project.results.edit', [$project->id, $modal->primaryid])."' title='Edit'> <i class='fa fa-edit'></i></a>";
                            //}
                        })
                        ->editColumn('state', function ($modal) use ($project){
                            $state = (!is_null($modal->state))? $modal->state:'';
                            return _t($state);
                        })
                        ->editColumn('township', function ($modal) use ($project){
                            $township = (!is_null($modal->township))? $modal->township:'';
                            return _t($township);
                        })
                        ->editColumn('village', function ($modal) use ($project){
                            $village = (!is_null($modal->village))? $modal->village:'';
                            return _t($village);
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
        $area = $request->get('area');
        $location = ($request->has('location'))? $request->get('location'):'*';
        $org_id = $request->get('org');
        // find correct organization object
        $org = $this->organizations->findOrThrowException($org_id);
        
        $result = $this->plocation->searchLocations($term, $location, $area, $org->id, $location, 'asc');
        
        $data = $result->transform(function ($item, $key) use ($location) {
                    $item['value'] = $item[$location];
                    $item['label'] = $item[$location];
                    $item['key'] = $location;
                    return $item;
                });
        return response()->json($data);
    }
    
    public function delocate($pid, $lid, Request $request){
        $participant = $this->participant->findOrThrowException($pid);
        $json['status'] = $participant->pcode()->detach($lid);
        return json_encode($json);
    }
}
