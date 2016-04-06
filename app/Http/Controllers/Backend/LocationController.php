<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Location\CreateLocationRequest;
use App\Repositories\Backend\Location\LocationContract;
use App\Repositories\Backend\Organization\OrganizationContract;
use App\Repositories\Backend\Participant\ParticipantContract;
use App\Repositories\Backend\Participant\Role\RoleRepositoryContract;
use App\Repositories\Backend\PLocation\PLocationContract;
use App\Repositories\Backend\User\UserContract;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Input;
use Symfony\Component\HttpFoundation\Response;

class LocationController extends Controller
{
    protected $plocation;
    
    protected $location;
    
    protected $organizations;
    
    protected $participants;
    
    protected $proles;

    protected $users;

    public function __construct(PLocationContract $plocation, 
                                LocationContract $location, 
                                OrganizationContract $organizations, 
                                ParticipantContract $participants,
                                RoleRepositoryContract $proles,
                                UserContract $users) 
    {
        $this->plocations = $plocation;    
        $this->locations = $location;
        $this->organizations = $organizations;
        $this->participants = $participants;
        $this->proles = $proles;
        $this->users = $users;
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {

        $current_user = $this->users->findOrThrowException(access()->id(), true, true);
        if($current_user->organization){
            $org_id = $current_user->organization->id;
        }else{
            $org_id = false;
        }
        $plocations = $this->plocations->getLocationsPaginatedTable(config('aio.location.default_per_page'),$org_id, true);
        $alltownships = $this->plocations->getAllLocations('township')->lists('township', 'township');
        $alldistricts = $this->plocations->getAllLocations('district')->lists('district', 'district');
        $allstates = $this->plocations->getAllLocations('state')->lists('state', 'state');
        
            return view('backend.location.locations', compact('plocations','alltownships','alldistricts','allstates', 'org_id'));
    }
    
    

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('backend.location.create')
            ->withLocations($this->locations->getAllLocations('name'))
            ->withOrganizations($this->organizations->getAllOrganizations('name', 'asc', ['pcode', 'projects']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(CreateLocationRequest $request)
    {
        $this->plocations->create(
			$request->except('org_id', 'location_id'),
			$request->only('org_id'),
                        $request->only('location_id')
		);
	return redirect()->route('admin.locations.index')->withFlashSuccess('The location was successfully created.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        dd('show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function deleteAll(Request $request)
    {
        if(access()->user()->role->level < 2){
            if($request->get('org_id')){
               $organization = $this->organizations->findOrThrowException($request->get('org_id'));
            }
        }else{
            return redirect()->back()->withFlashDanger('You are not allowed to do that.');
        }
        \DB::table('answers')->delete();
        \DB::table('results')->delete();
        \DB::table('translations')->delete();
        \DB::table('participants')->delete();
        \DB::table('pcode')->delete();
        //\App\Participant::where('org_id', $organization->id)->forceDelete();
        //\App\PLocation::where('org_id', $organization->id)->delete();
        //$organization->pcode()->delete();
        
        return redirect()->back()->withFlashSuccess('All data was successfully deleted.');
        
    }
    /**
     * 
     * 
     */
    public function search() {
        
        $query = Input::get('q');
        //$search_by = Input::get('search_by');
        $search_by = 'village';
        $order_by = ((null !== Input::get('field'))? Input::get('field'): 'name');
        $sort = ((null !== Input::get('sort'))? Input::get('sort'): 'asc');
        $location = $this->locations->searchLocations($query, $search_by, $order_by, $sort)->has('pcode')->get();//dd($location->get());
        $total = $location->count();
        $pageName = 'page';
        $per_page = config('access.users.default_per_page');
        $page = null;
        //Create custom pagination
        $plocations = new LengthAwarePaginator($location, $total, $per_page, $page, [
                            'path' => Paginator::resolveCurrentPath(),
                            'pageName' => $pageName,
                            'query' => ['search_by' => $search_by, 'q' => $query],
                        ]);
        //dd($locations);
        if($plocations->count() == 0){
            return redirect()->route('admin.locations.index')->withFlashDanger('Your search term "'.$query.'" not found!');
        }
        $alltownships = $this->locations->getTownshipsScope('MMR')->lists('name','id');
        $alldistricts = $this->locations->getDistrictsScope('MMR')->lists('name','id');
        $allstates = $this->locations->getStatesScope('MMR')->lists('name','id');
        //$locations = $this->locations->getLocationsPaginatedTable(config('access.locations.default_per_page'), 'village');
           //dd($locations); 
        $current_user = $this->users->findOrThrowException(access()->id(), true, true);
        if($current_user->organization){
            $org_id = $current_user->organization->id;
        }else{
            $org_id = false;
        }
            return view('backend.location.locations', compact('plocations', 'search_by', 'query', 'alltownships','alldistricts','allstates', 'org_id'));
    }
    
    public function showImport(){
        
        return view('backend.location.import')
			->withOrganizations($this->organizations->getAllOrganizations('name', 'asc', ['pcode','projects']))
                        ->withProles($this->proles->getAllRoles('name', 'asc'));
    }


    /**
     * 
     */
    public function import(Request $request) {
                //dd($request->all());
		$file = $request->only('file')['file'];
                $org = $request->only('organization')['organization'];
                $codetype = $request->only('prole')['prole'];
		if (!empty($file)) {
			$file = $file->getRealPath();
                        
                        $exitCode = Artisan::call('emsdb:import', [
                                        '--file' => $file, '--org' => $org, '--filetype' => 'pcode', '--level' => $codetype
                                    ]);
                        
			$message = 'Location List imported!';
                        return redirect()->route('admin.locations.index')->withFlashSuccess($message);
		} else {
			$message = 'No file to import!';
                        return redirect()->route('admin.locations.index')->withFlashDanger($message);
		}
		
    }
}
