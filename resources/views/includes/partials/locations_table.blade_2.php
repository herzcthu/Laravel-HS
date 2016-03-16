        <div class="row">
            <div class='col-xs-12 col-sm-12 col-md-12'>
        {!! Form::open(['route' => 'admin.locations.search', 'class' => 'form-horizontal', 'role' => 'form', 'method' => 'get']) !!}
        <div class="form-inline">
            <div class="input-group hidden">
                <div class="form-inline">
                    <label class="control-label pull-left" for="search_by"> Search By -&nbsp;</label>
                {!! Form::select('search_by', ['village' => 'Village', 
                                            'township' => 'Township', 
                                            'district' => 'District', 
                                            'state' => 'State'], 
                                            Input::get('search_by')? Input::get('search_by'):'village', 
                                            ['class' => 'form-control disable', 'disable']) !!}
                </div>
            </div>
            <div class="input-group">
                
                <input name="q" class="form-control" placeholder="{!! Input::get('q')? Input::get('q'):'Search' !!}" type="text">
                <span class="input-group-btn">
                    <button type="submit" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i></button>
                </span>
            </div>
        
        </div>
        {!! Form::close() !!}
        </div>
        </div>

{!! 
    $plocations->columns([
    'pcode' => 'Location Code',
    'uec_code' => 'UEC Code',
    'village' => 'Village',
    'village_tract' => 'Village Track',
    'township' => ['name'=>'Township', 'html' => "<select id='township_filter' class='form-control form-search'>".Aio()->createSelectBoxFromArray($alltownships)."</select>"],
    'district' => ['name'=>'District', 'html' => "<select id='district_filter' class='form-control form-search'>".Aio()->createSelectBoxFromArray($alldistricts)."</select>"],
    'state' => ['name'=>'State', 'html' => "<select id='state_filter' class='form-control form-search'>".Aio()->createSelectBoxFromArray($allstates)."</select>"]
    ]
    )
    ->modify('pcode',function($location) use ($org_id) {
        if(!is_null($location->location)){
            return $location->pcode;  
        }else{
            if($org_id){
                return $location->pcode()->where('org_id', $org_id)->first()->pcode;
            }else{
                return $location->pcode.' (global)';
            }
        }
    })
    ->modify('uec_code',function($location) use ($org_id) {
        if(!is_null($location->location)){
            return $location->uec_code;  
        }else{
            if($org_id){
                return $location->pcode()->where('org_id', $org_id)->first()->uec_code;
            }else{
                return 'None';
            }
        }
    })
    ->modify('village', function($location) { 
        if(!is_null($location->location)){
            return $location->location->ancestorsAndSelf()->whereType('village')->first()->name;  
        }elseif(is_null($location->location_id)){
            return;
        }else{
            return $location->name;
        }
        
    })
    ->modify('village_tract', function($location) {
        if(!is_null($location->location)){
            return $location->location->ancestorsAndSelf()->whereType('village_tract')->first()->name; 
        }elseif(is_null($location->location_id)){
            return;
        }else{
            return $location->ancestorsAndSelf()->whereType('village_tract')->first()->name;
        }
    })
    ->modify('township', function($location) { 
        if(!is_null($location->location)){
            return $location->location->ancestorsAndSelf()->whereType('township')->first()->name; 
        }elseif(is_null($location->location_id)){
            return;
        }else{
            return $location->ancestorsAndSelf()->whereType('township')->first()->name; 
        }
    })
    ->modify('district', function($location) {
        if(!is_null($location->location)){
            return $location->location->ancestorsAndSelf()->whereType('district')->first()->name;  
        }elseif(is_null($location->location_id)){
            return;
        }else{
            return $location->ancestorsAndSelf()->whereType('district')->first()->name;
        }
    })
    ->modify('state', function($location) {
        if(!is_null($location->location)){
            return $location->location->ancestorsAndSelf()->whereType('state')->first()->name;    
        }elseif(is_null($location->location_id)){
            return;
        }else{
            return $location->ancestorsAndSelf()->whereType('state')->first()->name; 
        }
    })
    ->render('includes.partials.laravel-5-table');
            
!!}
    <div class="pull-left">
        {!! $plocations->total() !!} {{ isset($search_by)?$search_by:'location' }}(s) total
    </div>

    <div class="pull-right">
        {!! $plocations->render() !!}
    </div>
<div class="clearfix"></div>
