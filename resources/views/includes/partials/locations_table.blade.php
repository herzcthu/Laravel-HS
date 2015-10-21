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
    
    ->render('includes.partials.laravel-5-table');
            
!!}
    <div class="pull-left">
        {!! $plocations->total() !!} {{ isset($search_by)?$search_by:'location' }}(s) total
    </div>

    <div class="pull-right">
        {!! $plocations->render() !!}
    </div>
<div class="clearfix"></div>
