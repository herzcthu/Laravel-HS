<?php

function getAnces($locations, $type){
    $ances = $locations->getAncestorsWithoutRoot()->lists('name','type')->toArray();
    if(array_key_exists($type, $ances)) {
        return $ances[$type];
    }else{
        return false;
    }
}

if(null !== Input::get('search_by')){
    $search_by = Input::get('search_by');
    if(Input::get('search_by') == 'state'){
        $columns = [
         'name' => 'State',
         'mya_name' => 'မြန်မာအမည်',
         'country' => 'Country',
        ];
    }elseif(Input::get('search_by') == 'district'){
        $columns = [
         'name' => 'District',
         'mya_name' => 'မြန်မာအမည်',
         'state' => 'State',
         'country' => 'Country',
        ];
    }elseif(Input::get('search_by') == 'township'){
        $columns = [
         'name' => 'Township',
         'mya_name' => 'မြန်မာအမည်',
         'district' => 'District',
         'state' => 'State',
         'country' => 'Country',
        ];
    }elseif(Input::get('search_by') == 'village_tract'){
        $columns = [
         'name' => 'Village Track',
         'mya_name' => 'မြန်မာအမည်',
         'tsp' => 'Township',
         'district' => 'District',
         'state' => 'State',
         'country' => 'Country',
        ];
    }else{
      $columns = [
         'name' => 'Village',
         'mya_name' => 'မြန်မာအမည်',
         'vtrack' => 'Village Track',
         'tsp' => 'Township',
         'district' => 'District',
         'state' => 'State',
         'country' => 'Country',
        ];  
    }
    
}else{
    $columns = [
         'name' => 'Village',
         'mya_name' => 'မြန်မာအမည်',
         'vtrack' => 'Village Track',
         'tsp' => 'Township',
         'district' => 'District',
         'state' => 'State',
         'country' => 'Country',
    ];
}
?> 
        <div class="row">
            <div class='col-xs-12 col-sm-12 col-md-12'>
        {!! Form::open(['route' => 'admin.locations.search', 'class' => 'form-horizontal', 'role' => 'form', 'method' => 'get']) !!}
        <div class="form-inline">
            <div class="input-group">
                <div class="form-inline">
                    <label class="control-label pull-left" for="search_by"> Search By -&nbsp;</label>
                {!! Form::select('search_by', ['village' => 'Village', 
                                            'township' => 'Township', 
                                            'district' => 'District', 
                                            'state' => 'State'], 
                                            Input::get('search_by')? Input::get('search_by'):'village', 
                                            ['class' => 'form-control']) !!}
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
    $location_table = $locations->columns($columns)
   // ->modify('id', function($locations){
   //         return false;      
   // })
    ->modify('name', function($locations) {
            return $locations->name;        
    })
    ->modify('mya_name', function($locations) {
            return $locations->mya_name;        
    })
    ->modify('vtrack', function($locations) {
                    return getAnces($locations, 'village_tract');
    })
    ->modify('tsp', function($locations) {
                    return getAnces($locations, 'township');
    })
    ->modify('district', function($locations) {
                    return getAnces($locations, 'district');
    })
    ->modify('state', function($locations) {
                    return getAnces($locations, 'state');
    })
    ->modify('country', function($locations) {
            return $locations->getRoot()->name;        
    })
    ->sortable(array('name', 'mya_name'))
    //->showPages() /doesn't work in laravel 5
    ->render('includes.partials.laravel-5-table');
            
!!}
    <div class="pull-left">
        {!! $locations->total() !!} {{ isset($search_by)?$search_by:'location' }}(s) total
    </div>

    <div class="pull-right">
        {!! $locations->render() !!}
    </div>
<div class="clearfix"></div>
