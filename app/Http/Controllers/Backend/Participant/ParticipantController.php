<?php namespace App\Http\Controllers\Backend\Participant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Participant\BulkUpdateParticipantsRequest;
use App\Http\Requests\Backend\Participant\CreateParticipantRequest;
use App\Http\Requests\Backend\Participant\UpdateParticipantRequest;
use App\Repositories\Backend\Location\LocationContract;
use App\Repositories\Backend\Organization\OrganizationContract;
use App\Repositories\Backend\Participant\ParticipantContract;
use App\Repositories\Backend\Participant\Role\RoleRepositoryContract;
use App\Repositories\Backend\Permission\PermissionRepositoryContract;
use App\Repositories\Backend\PLocation\PLocationContract;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;

/**
 * Class ParticipantController
 */
class ParticipantController extends Controller {

	/**
	 * @var ParticipantContract
	 */
	protected $participants;
	/**
	 * @var RoleRepositoryContract
	 */
	protected $roles;

	/**
	 * @var PermissionRepositoryContract
	 */
	protected $permissions;
        
        Protected $plocation;

	/**
	 * @param ParticipantContract $users
	 * @param RoleRepositoryContract $roles
	 * @param PermissionRepositoryContract $permissions
	 */
	public function __construct(
		ParticipantContract $participants,
		RoleRepositoryContract $roles, 
                LocationContract $locations,
                OrganizationContract $organizations,
                PLocationContract $plocation
                ) {
		$this->participants = $participants;
		$this->roles = $roles;
                $this->locations = $locations;
                $this->organizations = $organizations;
                $this->plocation = $plocation;
	}

	/**
	 * @return mixed
	 */
	public function index() {
		return view('backend.participant.index')
			->withParticipants($this->participants->getParticipantsPaginated(config('aio.participant.default_per_page'), 1))
                        ->withRoles($this->roles->getAllRoles('id', 'asc', true));
	}

	/**
	 * @return mixed
	 */
	public function create() {
                javascript()->put([
			'url' => [//'state' => route('ajax.locations.allstates'),
                            'state' => null,
                            'district' => route('ajax.locations.districts_by_id'),
                            'township' => route('ajax.locations.townships_by_id'),
                            'villagetrack' => route('ajax.locations.villagetracks_by_id'),
                            'village' => route('ajax.locations.villages_by_id')
                            ]
		]);
		return view('backend.participant.create')
			->withRoles($this->roles->getAllRoles('id', 'asc', true))
                        ->withLocations($this->locations->getStatesScope(config('aio.country'))->orderBy('name', 'asc')->lists('name','id'));
	}

	/**
	 * @param CreateParticipantRequest $request
	 * @return mixed
	 */
	public function store(CreateParticipantRequest $request) {
            //dd($request->all());
		$this->participants->create(
			$request->except('role', 'locations'),
			$request->only('role'),
			$request->only('locations')
		);
		return redirect()->route('admin.participants.index')->withFlashSuccess('The user was successfully created.');
	}

	/**
	 * @param $id
	 * @return mixed
	 */
	public function edit($id, Request $request) {
            javascript()->put([
			'url' => [//'state' => route('ajax.locations.allstates'),
                            'state' => '',
                            'district' => route('ajax.locations.districts_by_id'),
                            'township' => route('ajax.locations.townships_by_id'),
                            'villagetrack' => route('ajax.locations.villagetracks_by_id'),
                            'village' => route('ajax.locations.villages_by_id')
                            ]
		]);
            $view = View::make('includes.partials.medialist_grid');
		$participant = $this->participants->findOrThrowException($id, true);
                if ($request->ajax()) {
                    
                   $sections = $view->renderSections(); 
                   
                    return json_encode($sections['mediagrid']);
			//return Response::json(view('', compact('posts'))->render());
                } else {
		return view('backend.participant.edit')
			->withParticipant($participant)
			->withRoles($this->roles->getAllRoles('id', 'asc', true))
                        ->withLocations($this->locations)
                        ->withPLocation($this->plocation);
                }
	}

	/**
	 * @param $id
	 * @param UpdateParticipantRequest $request
	 * @return mixed
	 */
	public function update($id, UpdateParticipantRequest $request) {            
		$this->participants->update($id,
			$request->except('role', 'locations'),
			$request->only('role'),
			$request->only('locations')
		);
		return redirect()->route('admin.participants.index')->withFlashSuccess('The user was successfully updated.');
	}
        
        public function showImport() {
            return view('backend.participant.import')
			->withRoles($this->roles->getAllRoles('id', 'asc', true))
                        ->withOrganizations($this->organizations->getAllOrganizations('name', 'asc', ['pcode','users','projects']));
        }
        
	public function import(Request $request) {
                //dd($request->all());
		$file = $request->only('file')['file'];
                $role = $request->only('role')['role'];
                $org = $request->only('organization')['organization'];
		if (!empty($file)) {
			$file = $file->getRealPath();
                        $exitCode = Artisan::call('emsdb:import', [
                                        '--file' => $file, '--org' => $org, '--filetype' => 'participant', '--level' => $role
                                    ]);
                        
			$message = 'Participant List imported!';
                        return redirect()->route('admin.participants.index')->withFlashSuccess($message);
		} else {
			$message = 'No file to import!';
                        return redirect()->route('admin.participants.index')->withFlashDanger($message);
		}
		
	}
	/**
	 * @param $id
	 * @return mixed
	 */
	public function destroy($id) {
		$this->participants->destroy($id);
		return redirect()->back()->withFlashSuccess('The user was successfully deleted.');
	}

	/**
	 * @param $id
	 * @return mixed
	 */
	public function delete($id) {
		$this->participants->delete($id);
		return redirect()->back()->withFlashSuccess('The user was deleted permanently.');
	}

	/**
	 * @param $id
	 * @return mixed
	 */
	public function restore($id) {
		$this->participants->restore($id);
		return redirect()->back()->withFlashSuccess('The user was successfully restored.');
	}

	/**
	 * @param $id
	 * @param $status
	 * @return mixed
	 */
	public function mark($id, $status) {
		$this->participants->mark($id, $status);
		return redirect()->back()->withFlashSuccess('The user was successfully updated.');
	}

	/**
	 * @return mixed
	 */
	public function deactivated() {
		return view('backend.participant.deactivated')
			->withParticipants($this->participants->getParticipantsPaginated(25, 0));
	}

	/**
	 * @return mixed
	 */
	public function deleted() {
		return view('backend.participant.deleted')
			->withParticipants($this->participants->getDeletedParticipantsPaginated(25));
	}

	        
        
        public function bulk(BulkUpdateParticipantsRequest $request) {
            //dd($this->users);
            /**
             * To Do: check if user is allowed to update roles.
             * Or current user is above the updating roles
             */
            $role = (int) $request->role;
            foreach($request->users as $id => $status) {
                $participants = $this->participants->findOrThrowException($id, true);
                $participant['name'] = $participants->name;
                $participant['status'] = $participants->status;
                $participant['confirmed'] = $participants->confirmed;
                $participant['email'] = $participants->email;
                $roles = $participants->roles->lists('id')->all();
                $permissions = $participants->permissions->lists('id')->all();
                //dd($role);
                array_push($roles, $role);
                //dd($roles);
                $this->participants->update($id, $participant, ['assignees_roles' => array_unique(array($role))], ['permission_participant' => $permissions]);
            }
            
            return redirect()->route('admin.participant.index')->withFlashSuccess('The users were successfully updated.');
        }
        
        /**
	 * @return mixed
	 */
	public function search() {
                $query = Input::get('q');
                $order_by = ((null !== Input::get('field'))? Input::get('field'): 'id');
                $sort = ((null !== Input::get('sort'))? Input::get('sort'): 'asc');
                $participant = $this->participants->searchParticipants($query, $order_by, $sort);
                $total = $participant->count();
                $pageName = 'page';
                $per_page = config('aio.participant.default_per_page');
                $page = null;
                //Create custom pagination
                $participants = new LengthAwarePaginator($participant, $total, $per_page, $page, [
                                    'path' => Paginator::resolveCurrentPath(),
                                    'pageName' => $pageName,
                                ]);
                if($participants->count() == 0){
                    return redirect()->route('admin.participants.index')->withFlashDanger('Your search term "'.$query.'" not found!');
                }
		return view('backend.participant.index', compact('participants'))
                        ->withRoles($this->roles->getAllRoles('id', 'asc', true));
	}
}