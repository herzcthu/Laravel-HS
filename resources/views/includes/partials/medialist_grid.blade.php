@section('mediagrid')
@if($allmedia)
<div id="mediabox">
<select id="mediagrid" class="image-picker">   

  @foreach($allmedia as $media)  
  <?php $json = json_decode($media->file, true);
        $collection = collect($json);
        $image = $collection->last();
        //print_r($image['square50']['filedir']);
        $url = $image['square100']['filedir'];
        $filename = $collection['original_filename'];
  ?>
  <option data-img-src="/{!! $url !!}" value="{!! $media->id !!}"> {!! $filename !!}  </option>
  
  @endforeach

</select>
<div id="pagination">
    {!! $allmedia->render() !!}
</div>  
</div>
@else
<div class="row">
<div id="mediabox" style="display: block;">
    <select id="mediagrid" class="image-picker">
        
    </select>
    <div id="pagination">
        
    </div>
</div>
</div>
@endif  
@endsection