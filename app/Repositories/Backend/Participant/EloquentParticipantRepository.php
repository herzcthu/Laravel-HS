<?php namespace App\Repositories\Backend\Participant;

use App\Exceptions\GeneralException;
use App\Participant;
use App\PLocation;
use App\Repositories\Backend\Organization\OrganizationContract;
use App\Repositories\Backend\Participant\Role\RoleRepositoryContract;
use App\Repositories\Frontend\Auth\AuthenticationContract;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Class EloquentParticipantRepository
 * @package App\Repositories\Participant
 */
class EloquentParticipantRepository implements ParticipantContract {
    
        /**
         * @var RoleRepositoryContract
	 */
	protected $role;
        
        /**         *
         * @var OrganizationContract
         */
        protected $organization;

        /**
	 * @var AuthenticationContract
	 */
	protected $auth;

	/**
         * @param PLocationContract $plocation
         * @param RoleRepositoryContract $role
         * @param AuthenticationContract $auth
         */
	public function __construct(
                                    RoleRepositoryContract $role,
                                    OrganizationContract $organization,
                                    AuthenticationContract $auth) {
		$this->role = $role;
                $this->organization = $organization;
		$this->auth = $auth;
	}

	/**
         * 
         * @param integer $id
         * @param boolean $withRoles
         * @return object
         * @throws GeneralException
         */
	public function findOrThrowException($id, $withRelations = false) {
		if ($withRelations)
			$participant = Participant::with('role')->with('pcode')->withTrashed()->find($id);
		else
			$participant = Participant::withTrashed()->find($id);

		if (! is_null($participant)) return $participant;

		throw new GeneralException('That participant does not exist.');
	}

	/**
	 * @param $per_page
	 * @param string $order_by
	 * @param string $sort
	 * @param int $status
	 * @return mixed
	 */
	public function getParticipantsPaginated($per_page, $status = 1, $order_by = 'id', $sort = 'asc') {
                $order_by = ((null !== Input::get('field'))? Input::get('field'):$order_by);
                $sort = ((null !== Input::get('sort'))? Input::get('sort'):$sort);
                if(!access()->user()->can('manage_organization')){
                    if(null !== access()->user()->organization){
                        return Participant::where('org_id', access()->user()->organization->id)->orderBy($order_by, $sort)->paginate($per_page);
                    }else{
                        throw new GeneralException('User is not organization member!');
                    }
                }else{
                    return Participant::orderBy($order_by, $sort)->paginate($per_page);
                }
	}
        
        /**
         * 
         * @param string $queue
         * @param boolean $status
         * @param string $order_by
         * @param string $sort
         * @return object
         */
	public function searchParticipants($queue, $order_by = 'name', $sort = 'asc') {
            $order_by = ((null !== Input::get('field'))? Input::get('field'):$order_by);
            $sort = ((null !== Input::get('sort'))? Input::get('sort'):$sort);
            return Participant::search($queue)->orderBy($order_by, $sort)->get();
	}

	/**
	 * @param $per_page
	 * @return Paginator
	 */
	public function getDeletedParticipantsPaginated($per_page) {
		return Participant::onlyTrashed()->paginate($per_page);
	}

        public function getParticipantByCode($pcode, $org_id){
            return Participant::where('participant_id', $pcode)->where('org_id', $org_id)->first();
        }
	/**
	 * @param string $order_by
	 * @param string $sort
	 * @return mixed
	 */
	public function getAllParticipants($order_by = 'name', $sort = 'asc') {
		return Participant::orderBy($order_by, $sort)->get();
	}

	/**
         * 
         * @param array $input
         * @param mixed $roles
         * @param mixed $locations
         * @return object
         * @throws GeneralException
         */
	public function create($input) {
                $organization = $this->organization->findOrThrowException($input['org_id']);
                $role = $this->role->findOrThrowException($input['role']);
                $area = ['village', 'village_tract', 'township', 'district', 'state', 'country'];
                
                $location = PLocation::where('org_id', $input['org_id'])->where('pcode', $input['pcode'])->first();
                // create participant instant from input
                $participant = $this->createParticipantStub($input, $organization->id);
                // associate with organization
                $participant->organization()->associate($organization);
                // associate with role
                $participant->role()->associate($role);
                // save participant to database
                $participant->save();
                
                // attach participant to pcode
                $participant->pcode()->attach($location->id);
                
		if ($participant) {                        
			return $participant;
		}

		throw new GeneralException('There was a problem creating this participant. Please try again.');
	}

	/**
         * 
         * @param integer $id
         * @param array $input
         * @param mixed $roles
         * @param mixed $locations
         * @return boolean
         * @throws GeneralException
         */
	public function update($participant, $input, $pcode, $org, $role_id) {
                $organization = $this->organization->findOrThrowException($org['org_id']);
                $role = $this->role->findOrThrowException($role_id['role']);
                $area = ['village', 'village_tract', 'township', 'district', 'state', 'country'];
                if(!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
                    // valid address
                    unset($input['email']);
                }
                if(empty($input['nrc_id'])){
                    $input['nrc_id'] = null;
                }
                // get participant
                //$participant = $this->findOrThrowException($id);
                
                // attach participant to pcode
                if(!empty($pcode['pcode_id'])){
                    $location = PLocation::where('org_id', $org['org_id'])->where('pcode', $pcode['pcode_id'])->first();
                    if(!empty($location)){
                        $participant->pcode()->attach($location); 
                    }else{
                        return false;
                    }
                }               
                
                // dissociate old organization
                $participant->organization()->dissociate();
                // associate with updated organization
                $participant->organization()->associate($organization);
                // dissociate old role
                $participant->role()->dissociate();
                // associate with updated role
                $participant->role()->associate($role);
                
                
		if ($participant->update($input)) {
			return true;
		}

		throw new GeneralException('There was a problem updating this participant. Please try again.');
	}

	

	/**
	 * @param $id
	 * @return bool
	 * @throws GeneralException
	 */
	public function destroy($id) {
		
		$participant = $this->findOrThrowException($id);
		if ($participant->delete())
			return true;

		throw new GeneralException("There was a problem deleting this participant. Please try again.");
	}

	/**
	 * @param $id
	 * @return boolean|null
	 * @throws GeneralException
	 */
	public function delete($id) {
		$participant = $this->findOrThrowException($id, true);

		//Detach all roles & permissions
		$participant->detachRoles($participant->roles);

		try {
			$participant->forceDelete();
		} catch (\Exception $e) {
			throw new GeneralException($e->getMessage());
		}
	}

	/**
	 * @param $id
	 * @return bool
	 * @throws GeneralException
	 */
	public function restore($id) {
		$participant = $this->findOrThrowException($id);

		if ($participant->restore())
			return true;

		throw new GeneralException("There was a problem restoring this participant. Please try again.");
	}

	/**
	 * @param $id
	 * @param $status
	 * @return bool
	 * @throws GeneralException
	 */
	public function mark($id, $status) {
		if (auth()->id() == $id && ($status == 0 || $status == 2))
			throw new GeneralException("You can not do that to yourself.");

		$participant = $this->findOrThrowException($id);
		$participant->status = $status;

		if ($participant->save())
			return true;

		throw new GeneralException("There was a problem updating this participant. Please try again.");
	}
        
        
        public function participantsDataSet($p, $org, \App\PLocation $location) {
            //dd($p);
            /**
            $obpair = [];
            foreach ($p as $keys => $val){
                preg_match_all('/([[:alnum:]]+)(?:_)?/', $keys, $match);
                
                if(isset($match[1][1])){
                    if(preg_match('/^[a-zA-Z]$/',$match[1][1])){
                        
                        $obpair[$match[1][0].$match[1][1]]['type'] = $match[1][0];
                        $obpair[$match[1][0].$match[1][1]]['key'] = $match[1][1];
                    }else{
                        $obpair[$match[1][0]]['type'] = $match[1][0];
                        $obpair[$match[1][0]]['key'] = $match[1][1];
                    }
                }
            }
            print_r($obpair);
            die();
             * 
             */
            if(isset($p->supervisor_name)){
                $spv['name'] = $p->supervisor_name;
                $spv['id'] = $p->supervisor_id;
                $spvrole = $this->role->findRoleByName('Supervisor');
                if(is_null($spvrole)) {
                    $sinput['name'] = 'Supervisor';
                    $sinput['level'] = 4;
                    $spvrole = $this->role->create($sinput);
                }
                $spv['role_id'] = $spvrole->id;
                $spv['org_id'] = $org;
                $sattr = ['participant_code' => $p->supervisor_id, 'org_id' => $org, 'name' => $p->supervisor_name];    
                $supervisor = \App\Participant::updateOrCreate($sattr,$spv);
            }
            if(isset($p->pcode)){
                $pcode = (string) $p->pcode;
                //$observer['B']['pcode'] = (string) $p->pcode;
                //$observer['A']['uec_code'] = $p->uec_polling_station_code;
                //$observer['B']['uec_code'] = $p->uec_polling_station_code;
            }else{
                throw new GeneralException('No valid location code found! Check your upload file!');
            }
            
            if(isset($p->enumerator_name_english)){
                $observer['name'] = $p->enumerator_name_english;
            } else {
                $observer['name'] = 'No Name';
            }
            
            if(isset($p->phone_no_primary)){
                $observer['phones']['primary'] = (string) $p->phone_no_primary;                
            }
            if(isset($p->phone_no_primary)){
                $observer['phones']['secondary'] = (string) $p->phone_no_secondary;                
            }
            
            if(isset($p->enumerators_nrc_card)){
                $observer['nrc_id'] = $p->enumerators_nrc_card;
            }else{
                $observer['nrc_id'] = null;
            }
            
            if(isset($p->dob)){
                $observer['dob'] = $p->dob;
            }
            
            if(isset($p->gender)){
                $observer['gender'] = (is_null($p->gender)? 'Not Specified':$p->gender);
            }
            
            if(isset($p->address)){
                $observer['address'] = $p->address;
            }
            
            if(isset($p->email)){
                $observer['email'] = $p->email;
            }                       
            
            
            if(isset($p->enumerator_id)){
                    $role = $this->role->findRoleByName('Enumerator');
                    if(is_null($role)) {
                        $input['name'] = 'Enumerator';
                        $input['level'] = 0;
                        $role = $this->role->create($input);
                    }
                //foreach($observer as $key => $person){
                    $participant_code = (string) $p->enumerator_id;                    
                    $observer['role_id'] = $role->id;
                    $observer['org_id'] = $org;
                    //$place = $this->pcode->findOrThrowException($observer['pcode'].'-'.$org);
                    $attr = ['participant_code' => $participant_code, 'org_id' => $org, 'nrc_id' => $observer['nrc_id']];
                    
                    $participant = \App\Participant::updateOrCreate($attr,$observer);
                    if(isset($supervisor)){
                        $participant->supervisor()->associate($supervisor);
                        $participant->save();
                    }
                    // detach first to avoid duplicate
                    $location->participants()->detach($participant);
                    $location->participants()->attach($participant);
                    $location->save();
                //}
                    
            }else{
                throw new GeneralException('No valid participant found!');
            }
        }

	

	/**
	 * @param $roles
	 * @param $participant
	 */
	private function flushRoles($roles, $participant)
	{
		//Flush roles out, then add array of new ones
		$participant->detachRoles($participant->roles);
		$participant->attachRole($roles['role']);
	}

	/**
	 * @param $permissions
	 * @param $participant
	 */
	private function flushLocations($locations, $participant)
	{
		
                //Flush permissions out, then add array of new ones if any
		$participant->detachLocations($participant->locations);
		$participant->attachLocation($locations);
	}

	/**
	 * @param $roles
	 * @throws GeneralException
	 */
	private function checkParticipantRolesCount($roles)
	{
		//Participant Updated, Update Roles
		//Validate that there's at least one role chosen
		if (count($roles['role']) == 0)
			throw new GeneralException('You must choose at least one role.');
	}

	/**
	 * @param $input
	 * @return mixed
	 */
	private function createParticipantStub($input, $org)
	{   
		$attributes['participant_id'] = (array_key_exists('participant_id', $input)? $input['participant_id']:null);
                if(array_key_exists('nrc_id', $input)){
                    if($input['nrc_id']){
                        $nrc_id = $input['nrc_id'];
                    }else{
                        $nrc_id = null;
                    }
                }else{
                    $nrc_id = null;
                }
                $participant = Participant::firstOrNew(['participant_id' => $attributes['participant_id'], 'org_id' => $org, 'nrc_id' => $nrc_id]);
                
		$participant->name = (array_key_exists('name', $input)? $input['name']:'No Name');
                $participant->avatar = (array_key_exists('avatar', $input)? $input['avatar']:'');
		$participant->email = (array_key_exists('email', $input)? $input['email']:'No Email');
                if(array_key_exists('nrc_id', $input)){
                    if($input['nrc_id']){
                        $participant->nrc_id = $input['nrc_id'];
                    }else{
                        $participant->nrc_id = null;
                    }
                }else{
                    $participant->nrc_id = null;
                }
                $participant->dob = (array_key_exists('dob', $input)? $input['dob']:'');
                $array = [];
                $participant->phones = (array_key_exists('phones', $input)? $input['phones']:$array);
                //$participant->base = (array_key_exists('base', $input)? $input['base']:'');
                $participant->gender = (array_key_exists('gender', $input)? $input['gender']:'Not Specified');
                $participant->participant_id = (array_key_exists('participant_id', $input)? $input['participant_id']:null);
                
		return $participant;
	}
        
        public function cliImport($file, $org, $role) {
            set_time_limit(0);
            $excel = Excel::filter('chunk')->load($file, 'UTF-8')->chunk(250, function($participant) use ($file, $org, $role){
                           
                            $participant->each(function($row) use ($role, $org) { dd($row);
                                $this->participantsDataSet($row, $org, $role);
                            });
                        });
        }
}