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
	public function update($id, $input, $pcode, $org, $role_id) {
                $organization = $this->organization->findOrThrowException($org['org_id']);
                $role = $this->role->findOrThrowException($role_id['role']);
                $area = ['village', 'village_tract', 'township', 'district', 'state', 'country'];
                
                // get participant
                $participant = $this->findOrThrowException($id);
                
                // attach participant to pcode
                if(!empty($pcode['plcode'])){
                    $location = PLocation::where('org_id', $org['org_id'])->where('pcode', $pcode['plcode'])->first();
                    if(!empty($location)){
                        $participant->pcode()->attach($location->id); 
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
        
        
        public function participantsDataSet($p, $org, $role, \App\PLocation $location) {
            
            
            if(isset($p->pcode)){
                $observer['A']['pcode'] = (string) $p->pcode;
                $observer['B']['pcode'] = (string) $p->pcode;
                //$observer['A']['uec_code'] = $p->uec_polling_station_code;
                //$observer['B']['uec_code'] = $p->uec_polling_station_code;
            }else{
                throw new GeneralException('No valid location code found! Check your upload file!');
            }
            
            if(isset($p->observer_a_name_burmese)){
                $observer['A']['name'] = $p->observer_a_name_burmese;
            } else {
                $observer['A']['name'] = 'No Name';
            }
            if(isset($p->observer_b_name_burmese)){                
                $observer['B']['name'] = $p->observer_b_name_burmese;
            }else{
                $observer['B']['name'] = 'No Name';
            }
            
            if(isset($p->observer_a_mobile) && isset ($p->observer_b_mobile)){
                $observer['A']['phones']['mobile'] = (string) $p->observer_a_mobile;
                $observer['B']['phones']['mobile'] = (string) $p->observer_b_mobile;
                $observer['A']['phones']['emergency'] = (string) $p->emergency_contact_phone_observer_a;
                $observer['B']['phones']['emergency'] = (string) $p->emergency_contact_phone_observer_b;
            }
            
            if(isset($p->observer_a_nrc_card) && isset ($p->observer_b_nrc_card)){
                $observer['A']['nrc_id'] = $p->observer_a_nrc_card;
                $observer['B']['nrc_id'] = $p->observer_b_nrc_card;
            }
            
            if(isset($p->observer_a_birthdate) && isset ($p->observer_b_birthdate)){
                $observer['A']['dob'] = $p->observer_a_birthdate;
                $observer['B']['dob'] = $p->observer_b_birthdate;
            }
            
            if(isset($p->observer_a_gender) && isset ($p->observer_b_gender)){
                $observer['A']['gender'] = (is_null($p->observer_a_gender)? 'Not Specified':$p->observer_a_gender);
                $observer['B']['gender'] = (is_null($p->observer_b_gender)? 'Not Specified':$p->observer_b_gender);
            }
            
            if(isset($p->observer_a_addres) && isset ($p->observer_b_address)){
                $observer['A']['address'] = $p->observer_a_address;
                $observer['B']['address'] = $p->observer_b_address;
            }
            
            if(isset($p->observer_a_email) && isset ($p->observer_b_email)){
                $observer['A']['email'] = $p->observer_a_email;
                $observer['B']['email'] = $p->observer_b_email;
            }
            
            /**
            if(isset($p->village_tractward_burmese)){
                $observer['A']['villagetract'] = $p->village_tractward_burmese;
                $observer['B']['villagetract'] = $p->village_tractward_burmese;
                //$observer['A']['base'] = $p->village_tractward_burmese;
                //$observer['B']['base'] = $p->village_tractward_burmese;
            }
            if(isset($p->township_english)){
                $observer['A']['township'] = $p->township_english;
                $observer['B']['township'] = $p->township_english;
            }
            if(isset($p->district_english)){
                $observer['A']['district'] = $p->district_english;
                $observer['B']['district'] = $p->district_english;
            }
            
            if(isset($p->state_region_english)){
                $observer['A']['state'] = $p->state_region_english;
                $observer['B']['state'] = $p->state_region_english;
            }
             * 
             */
            
            $role = $this->role->findOrThrowException($role);
            if(isset($observer)){
                foreach($observer as $key => $person){
                    $person['participant_code'] = $person['pcode'].$key;
                    /**
                    if(isset($p->supervisor_name)){
                        if(!is_null($p->supervisor_id)){//dd($p);
                            $person['supervisor']['participant_id'] = $p->supervisor_id;
                        }else{
                            $person['supervisor']['participant_id'] = 'SV'.substr($p->pcode, 0, 3);
                        }
                        $person['supervisor']['location'] = $p->supervisor_location;
                        $person['supervisor']['name'] = $p->supervisor_name;
                        $person['supervisor']['base'] = (!is_null($p->supervisor_location)? $p->supervisor_location:$p->state_region_english);
                    }
                    if(isset($p->spot_checker_name)){
                        if(!is_null($p->spot_checker_id)){
                            $person['spot_checker']['participant_id'] = $p->spot_checker_id;
                        }else{
                            $person['spot_checker']['participant_id'] = 'SC'.substr($p->pcode, 0, 3);
                        }
                        $person['spot_checker'] = isset($p->spot_checker_name)? $p->spot_checker_name:'No Name';
                    }
                     * 
                     */
                    $person['role_id'] = $role->id;
                    $person['org_id'] = $org;
                    //$place = $this->pcode->findOrThrowException($person['pcode'].'-'.$org);
                    $attr = ['participant_code' => $person['participant_code'], 'org_id' => $org];
                    unset($person['pcode']);
                    $participant = \App\Participant::updateOrCreate($attr,$person);
                    // detach first to avoid duplicate
                    $location->participants()->detach($participant->id);
                    $location->participants()->attach($participant);
                    $location->save();
                }
                    
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