{!!
    $medias->columns(array(
       // 'no' => 'No.',
        'view' => 'View',
        'filename' => 'File Name',
        'filedir' => 'File Path',
        'owned_by' => 'Owned By',
        
    ))
    //->modify('no', function($medias) {
    //    return count($medias);
    //})
    ->means('owned_by', 'owner')
    ->modify('owned_by', function($users, $medias) {    
        return $users->name;
    })
    ->modify('view', function($medias) {
        if($medias->status !== 1){
        $json = json_decode($medias->file, true);
        $collection = collect($json);
        $image = $collection->get('dimensions');
        $url = $image['square50']['filedir'];
        $filename = $collection['original_filename'];
        $image = "<a href='/$url' target='_blank'><img src='/$url' alt='$filename'></a>";
        }else{
            $path = storage_path();
            $file = str_replace($path, '', $medias->filedir);
            $filedir = url('download'.$file);
            $image = "<a href='$filedir' target='_blank'><img width='50px' src='".asset('img/excel.png')."' alt='$medias->filename'></a>";
        }
        return $image;
    })
    ->modify('filename', function($medias) {
        if($medias->status !== 1){
        $json = json_decode($medias->file, true);
        $collection = collect($json);
        $image = $collection->get('dimensions');
        $url = $image['square50']['filedir'];
        $filename = $collection['original_filename'];
        $image = "<a href='/$url' target='_blank'>$filename</a>";
        }else{
            $path = storage_path();
            $file = str_replace($path, '', $medias->filedir);
            $filedir = url('download'.$file);
            $image = "<a href='$filedir' target='_blank'>$medias->filename</a>";
        }
        return $image;
    })
    ->modify('filedir', function($medias){
        if($medias->status !== 1){
            $filedir = asset($medias->filedir);
        }else{
            $path = storage_path();
            $file = str_replace($path, '', $medias->filedir);
            $filedir = url('download'.$file);
        }
        return $filedir;
    })
    ->render('includes.partials.laravel-5-table')
!!}

{!! $medias->render() !!}

