{!! 
    $plocations->columns([
    'pcode' => 'Location Code',
    'uec_code' => 'UEC Code',
    'village' => _t('Village'),
    'village_tract' => _t('Village Tract'),
    'township' => _t('Township'),
    'district' => _t('District'),
    'state' => _t('State')
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
