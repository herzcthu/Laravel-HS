<div class="row">
    <div class="col-md-12">
    <div class="pull-right" style="margin-bottom:10px">
        <div class="btn-group">
          <button type="button" class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
              Participants <span class="caret"></span>
          </button>
          <ul class="dropdown-menu" role="menu">
            <li><a href="{{route('admin.participants.index')}}">All Participants</a></li>
            <li><a href="{{route('admin.participants.create')}}">Create Participant</a></li>
            <li><a href="{{route('admin.participants.import')}}">Import Participant</a></li>
            <li class="divider"></li>
            <li><a href="{{route('admin.participants.deleted')}}">Deleted Users</a></li>
            
          </ul>
        </div>

        <div class="btn-group">
          <button type="button" class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
              Roles <span class="caret"></span>
          </button>
          <ul class="dropdown-menu" role="menu">
            <li><a href="{{route('admin.participants.proles.index')}}">All Roles</a></li>
            <li><a href="{{route('admin.participants.proles.create')}}">Create Role</a></li>
          </ul>
        </div>
    </div> 
<div class="pull-left" style="margin-bottom:10px">
        <div class="row">
        {!! Form::open(['route' => 'admin.participants.search', 'class' => 'form-horizontal', 'role' => 'form', 'method' => 'get']) !!}
        
        <div class='col-xs-12 col-sm-12 col-md-12 form-inline'>
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
    </div>
</div>    

    <div class="clearfix"></div>   
