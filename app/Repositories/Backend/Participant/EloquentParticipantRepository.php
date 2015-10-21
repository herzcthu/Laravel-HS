<?php namespace App\Repositories\Backend\Participant;

use App\Exceptions\GeneralException;
use App\Participant;
use App\Repositories\Backend\Location\LocationContract;
use App\Repositories\Backend\Organization\OrganizationContract;
use App\Repositories\Backend\Participant\Role\RoleRepositoryContract;
use App\Repositories\Backend\PLocation\PLocationContract;
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
         *
         * @var LocationContract
         */
        protected $locations;
    
        /**
         *
         * @var PLocationContract
         */
        protected $pcode;
        /**
	 * @var RoleRepositoryContract
	 */
	protected $role;
        
        /**
         *
         * @var OrganizationContract
         */
        protected $organization;

        /**
	 * @var AuthenticationContract
	 */
	protected $auth;

	/**
         * 
         * @param LocationContract $locations
         * @param PLocationContract $plocation
         * @param RoleRepositoryContract $role
         * @param AuthenticationContract $auth
         */
	public function __construct(LocationContract $locations,
                                    PLocationContract $plocation,
                                    RoleRepositoryContract $role,
                                    OrganizationContract $organization,
                                    AuthenticationContract $auth) {
                $this->locations = $locations;
                $this->pcode = $plocation;
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
	public function searchParticipants($queue, $status = 1, $order_by = 'id', $sort = 'asc') {
            $order_by = ((null !== Input::get('field'))? Input::get('field'):$order_by);
            $sort = ((null !== Input::get('sort'))? Input::get('sort'):$sort);
            return Participant::where('status', $status)->orderBy($order_by, $sort)->search($queue)->get();
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
         * @return boolean
         * @throws GeneralException
         */
	public function create($input, $locate, $role, $org) {
                $pcode_id = $input['pcode'].'-'.$org;
                $participant = $this->createParticipantStub($input, $pcode_id, $org); 
                $organization = $this->organization->findOrThrowException($org);
               
                $pcode['pcode'] = $input['pcode'];
                $pcode['uec_code'] = $input['uec_code'];
                        
                if(is_array($locate)){
                    $location_id = $locate['locations'];
                    $location = $this->pcode->findOrThrowException($location_id);
                    
                }elseif(is_object($locate)){
                    $location = $locate;
                }else{
                    
                }
                $located = $participant->pcode()->associate($location);
                
                if(is_array($role)){
                    $role_id = $role['role'];
                    $prole = $this->role->findOrThrowException($role_id);
                }

                if(is_object($role)){
                    $prole = $role;
                }
                //dd($plocation);
                $participant->role()->associate($prole);
                $participant->organization()->associate($organization);
                
                $participant->save();
		if ($participant) {
                        
			return true;
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
	public function update($id, $input, $roles, $locations) {
		$participant = $this->findOrThrowException($id);
		$this->checkParticipantByEmail($input, $participant);
                //dd(\Illuminate\Support\Facades\Input::file());
                
                
		if ($participant->update($input)) {                     

			
			$participant->save();

			$this->checkParticipantRolesCount($roles);
			$this->flushRoles($roles, $participant);
                        $this->flushLocations($locations['locations'][$this->role->getRoleLevel($roles['role'])], $participant);

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
        private function makeNestedSetArray($row, $org, $role) {
            foreach ($row as $p){
                $supervisorId = (string)$p->supervisor_id; 
                $nested[$supervisorId]['root']['participant_id'] = $supervisorId;
                $nested[$supervisorId]['root']['name'] = (string)$p->supervisor_name;
                $nested[$supervisorId]['root']['type'] = 'Supervisor';
                $nested[$supervisorId]['root']['org_id'] = $org;
                $nested[$supervisorId]['root']['base'] = (string)$p->supervisor_location;
                $nested[$supervisorId]['root']['pcode'] = (string) $p->custom_location_code;                                              
                $nested[$supervisorId]['root']['phones']['mobile'] = $p->spot_checker_mobile;
                $nested[$supervisorId]['root']['phones']['emergency'] = $p->spot_checker_emergency;
                $nested[$supervisorId]['root']['locations']['state'] = $p->stateregion_english;
                $nested[$supervisorId]['root']['locations']['district'] = $p->district_english;
                $nested[$supervisorId]['root']['locations']['township'] = $p->township_english;
                $nested[$supervisorId]['root']['locations']['village_tract'] = $p->village_tractward_burmese;
                $nested[$supervisorId]['root']['locations']['village'] = $p->polling_station_location_burmese;
                $nested[$supervisorId]['children'][0]['participant_id'] = $p->observer_a_id;
                $nested[$supervisorId]['children'][0]['name'] = $p->observer_a_name_burmese;
                $nested[$supervisorId]['children'][0]['type'] = 'Observer';
                $nested[$supervisorId]['children'][0]['base'] = $p->polling_station_location_burmese;
                $nested[$supervisorId]['children'][0]['dob'] = $p->observer_a_birthdate;
                $nested[$supervisorId]['children'][0]['nrc_id'] = $p->observer_a_nrc_card;
                $nested[$supervisorId]['children'][0]['gender'] = $p->observer_a_gender;
                $nested[$supervisorId]['children'][0]['pcode'] = (string) $p->custom_location_code;
                $nested[$supervisorId]['children'][0]['report_plan_a'] = $p->report_plan_a;
                $nested[$supervisorId]['children'][0]['report_plan_b'] = $p->report_plan_b;
                $nested[$supervisorId]['children'][0]['phones']['mobile'] = $p->observer_a_mobile;
                $nested[$supervisorId]['children'][0]['phones']['emergency'] = $p->emergency_contact_phone_observer_a;
                $nested[$supervisorId]['children'][0]['locations']['state'] = $p->stateregion_english;
                $nested[$supervisorId]['children'][0]['locations']['district'] = $p->district_english;
                $nested[$supervisorId]['children'][0]['locations']['township'] = $p->township_english;
                $nested[$supervisorId]['children'][0]['locations']['village_tract'] = $p->village_tractward_burmese;
                $nested[$supervisorId]['children'][0]['locations']['village'] = $p->polling_station_location_burmese;
                $nested[$supervisorId]['children'][1]['participant_id'] = $p->observer_b_id;
                $nested[$supervisorId]['children'][1]['name'] = $p->observer_b_name_burmese;
                $nested[$supervisorId]['children'][1]['type'] = 'Observer';
                $nested[$supervisorId]['children'][1]['base'] = $p->polling_station_location_burmese;
                $nested[$supervisorId]['children'][1]['dob'] = $p->observer_b_birthdate;        
                $nested[$supervisorId]['children'][1]['nrc_id'] = $p->observer_b_nrc_card;
                $nested[$supervisorId]['children'][1]['gender'] = $p->observer_b_gender;
                $nested[$supervisorId]['children'][1]['pcode'] = (string) $p->custom_location_code;
                $nested[$supervisorId]['children'][1]['report_plan_a'] = $p->report_plan_a;
                $nested[$supervisorId]['children'][1]['report_plan_b'] = $p->report_plan_b;                
                $nested[$supervisorId]['children'][1]['phone']['mobile'] = $p->observer_b_mobile;
                $nested[$supervisorId]['children'][1]['phone']['emergency'] = $p->emergency_contact_phone_observer_b;
                $nested[$supervisorId]['children'][1]['phones']['emergency'] = $p->emergency_contact_phone_observer_a;
                $nested[$supervisorId]['children'][1]['locations']['state'] = $p->stateregion_english;
                $nested[$supervisorId]['children'][1]['locations']['district'] = $p->district_english;
                $nested[$supervisorId]['children'][1]['locations']['township'] = $p->township_english;
                $nested[$supervisorId]['children'][1]['locations']['village_tract'] = $p->village_tractward_burmese;
                $nested[$supervisorId]['children'][1]['locations']['village'] = $p->polling_station_location_burmese;
                $nested[$supervisorId]['children'][2]['participant_id'] = (string) $p->spot_checker_id;
                $nested[$supervisorId]['children'][2]['name'] = isset($p->spot_checker_name)? $p->spot_checker_name:null;
                $nested[$supervisorId]['children'][2]['type'] = 'Spot Checker';
                $nested[$supervisorId]['children'][2]['pcode'] = (string) $p->custom_location_code;                                
                $nested[$supervisorId]['children'][2]['phones']['mobile'] = $p->spot_checker_mobile;
                $nested[$supervisorId]['children'][2]['phones']['emergency'] = $p->spot_checker_emergency;
                $nested[$supervisorId]['children'][2]['phones']['emergency'] = $p->emergency_contact_phone_observer_a;
                $nested[$supervisorId]['children'][2]['locations']['state'] = $p->stateregion_english;
                $nested[$supervisorId]['children'][2]['locations']['district'] = $p->district_english;
                $nested[$supervisorId]['children'][2]['locations']['township'] = $p->township_english;
                $nested[$supervisorId]['children'][2]['locations']['village_tract'] = $p->village_tractward_burmese;
                $nested[$supervisorId]['children'][2]['locations']['village'] = $p->polling_station_location_burmese;
                //dd($nested);
                $root = Participant::create($nested[$supervisorId]['root']);
                $root->makeTree($nested[$supervisorId]['children']);
            }
            
            return $nested;
        }
        
        public function arrayToNestedSet($p, $org, $role) {
            
            /**
             *  "pcode" => 117001.0
                "state_region" => "Ayeyarwady"
                "district" => "Pathein"
                "township" => "Pathein"
                "village_tracttown" => "Ah Lel (a) Ah Lel Kone (Ngwesaung Sub-township)"
                "villageward" => "Leik Inn Kone"
                "village_mya_mmr3" => "လိပ်အင်းကုန်း"
                "name" => null
                "mobile" => null
                "email_address_email" => null
                "national_id_number" => null
                "sex" => null
                "ethnicity" => null
                "date_of_birth" => null
                "education_background" => null
                "address" => null
             * 
             */
            /**
             * "stateregion_english" => "Ayeyarwady"
                "stateregion_burmese" => null
                "district_english" => "Hinthada"
                "township_english" => "Hinthada"
                "township_burmese" => null
                "uec_polling_station_code" => null
                "village_tractward_burmese" => null
                "polling_station_location_burmese" => null
                "observer_type" => 1.0
                "pace_location_code" => 10115.0
                "observer_a_id" => "10115A"
                "observer_a_name_burmese" => null
                "observer_a_gender" => null
                "observer_b_id" => "10115B"
                "observer_b_name_burmese" => null
                "observer_b_gender" => null
                "supervisor_id" => 10701.0
                "supervisor_location" => "Hinthada"
                "supervisor_name" => null
                "spot_checker_id" => 12801.0
                "report_plan_a" => 1.0
                "reporting_plan_b" => 2.0
                "observer_a_mobile" => null
                "observer_b_mobile" => null
                "emergency_contact_phone_observer_a" => null
                "emergency_contact_phone_observer_b" => null
                "observer_a_nrc_card" => null
                "observer_b_nrc_card" => null
                "observer_a_birthdate" => null
                "observer_b_birthdate" => null
             */
            //dd($p);
            $role = $this->role->findOrThrowException($role);
            if($role->name == 'Supervisor'){
                $supervisor['pcode'] = $p->supervisor_id;
                $supervisor['participant_id'] = $p->supervisor_id;
                if($role->level == 4){
                    $lkey = 'state';
                }
                if($role->level == 3){
                    $lkey = 'district';
                }
                if($role->level == 2){
                    $lkey = 'township';
                }
                if($role->level == 1){
                    $lkey = 'village_tract';
                }
                $supervisor[$lkey] = $supervisor->supervisor_location;
                $supervisor['name'] = $supervisor->name;
                $supervisor['base'] = $p->supervisor_location;
            }
            
            if($role->name == 'Spotchecker'){
                $spot_checker['pcode'] = $p->spot_checker_id;
                $spot_checker['participant_id'] = $p->spot_checker_id;
                if($role->level == 4){
                    $lkey = 'state';
                }
                if($role->level == 3){
                    $lkey = 'district';
                }
                if($role->level == 2){
                    $lkey = 'township';
                }
                if($role->level == 1){
                    $lkey = 'village_tract';
                }
                $spot_checker[$lkey] = $spot_checker->spot_checker_location;
                $spot_checker['name'] = $spot_checker->name;
            }
            
            if($role->name == 'Observer' && $role->level == 'village'){
                //loop $observer
            }
            
            
            if(isset($p->pcode)){
                $observer['A']['pcode'] = (string) $p->pcode;
                $observer['A']['uec_code'] = '';
            }elseif(isset($p->no)){
                /**
                 * "state" => "Kachin"
                    "region" => "Myitkyina"
                    "township" => null
                    "dob" => "07/30/1983"
                 */
                $observer['A']['pcode'] = (string) $p->no;
                $observer['A']['uec_code'] = '';
                $observer['A']['state'] = $p->state;
                $observer['A']['district'] = $p->region;
                $observer['A']['township'] = $p->township;
                $observer['A']['dob'] = $p->dob;
            }elseif(isset($p->custom_location_code)){
                $observer['A']['pcode'] = (string) $p->custom_location_code;
                $observer['B']['pcode'] = (string) $p->custom_location_code;
                $observer['A']['uec_code'] = $p->uec_polling_station_code;
                $observer['B']['uec_code'] = $p->uec_polling_station_code;
            }else{
                throw new GeneralException('No valid location code found! Check your upload file!');
            }
            
            if(isset($p->name)){
                $observer['A']['name'] = $p->name;
            }elseif(isset($p->observer_a_name_burmese) && isset($p->observer_b_name_burmese)){
                $observer['A']['name'] = $p->observer_a_name_burmese;
                $observer['B']['name'] = $p->observer_b_name_burmese;
            }else{
                $observer['A']['name'] = 'No Name';
            }
            
            if(isset($p->mobile)){
                $observer['A']['phone']['mobile'] = $p->mobile;
            }elseif(isset($p->observer_a_mobile) && isset ($p->observer_b_mobile)){
                $observer['A']['phone']['mobile'] = $p->observer_a_mobile;
                $observer['B']['phone']['mobile'] = $p->observer_b_mobile;
                $observer['A']['phone']['emergency'] = $p->emergency_contact_phone_observer_a;
                $observer['B']['phone']['emergency'] = $p->emergency_contact_phone_observer_b;
            }else{
                $observer['A']['phone']['mobile'] = 'No Phone';
            }
            
            if(isset($p->nrc_id)){
                $observer['A']['nrc_id'] = $p->nrc_id;
            }elseif(isset($p->observer_a_nrc_card) && isset ($p->observer_b_nrc_card)){
                $observer['A']['nrc_id'] = $p->observer_a_nrc_card;
                $observer['B']['nrc_id'] = $p->observer_b_nrc_card;
            }else{
                $observer['A']['nrc_id'] = null;
            }
            
            if(isset($p->date_of_birth)){
                $observer['A']['dob'] = $p->date_of_birth;
            }elseif(isset($p->observer_a_birthdate) && isset ($p->observer_b_birthdate)){
                $observer['A']['dob'] = $p->observer_a_birthdate;
                $observer['B']['dob'] = $p->observer_b_birthdate;
            }else{
                $observer['A']['dob'] = 'No Birthday';
            }
            
            if(isset($p->sex)){
                $observer['A']['gender'] = (is_null($p->sex)? 'Not Specified':$p->sex);
            }elseif(isset($p->observer_a_gender) && isset ($p->observer_b_gender)){
                $observer['A']['gender'] = (is_null($p->observer_a_gender)? 'Not Specified':$p->observer_a_gender);
                $observer['B']['gender'] = (is_null($p->observer_b_gender)? 'Not Specified':$p->observer_b_gender);
            }else{
                $observer['A']['gender'] = 'Not specified';
            }
            
            if(isset($p->address)){
                $observer['A']['address'] = $p->address;
            }elseif(isset($p->observer_a_addres) && isset ($p->observer_b_address)){
                $observer['A']['address'] = $p->observer_a_address;
                $observer['B']['address'] = $p->observer_b_address;
            }else{
                $observer['A']['address'] = 'No address';
            }
            
            if(isset($p->email_address_email)){
                $observer['A']['email'] = $p->email_address_email;
            }elseif(isset($p->observer_a_email) && isset ($p->observer_b_email)){
                $observer['A']['email'] = $p->observer_a_email;
                $observer['B']['email'] = $p->observer_b_email;
            }else{
                $observer['A']['email'] = 'Not specified';
            }
            if(isset($p->base)){
                $observer['A']['base'] = $p->base;
            }elseif(isset($p->district_english)){
                $observer['A']['base'] = $p->base;
                $observer['B']['base'] = $p->base;
            }else{
                
            }
            if(isset($p->supervisor_id)){
                $supervisor['participant_id'] = $p->supervisor_id;
                $supervisor['location'] = $p->supervisor_location;
                $supervisor['name'] = $p->supervisor_name;
                
                
            }
            if(isset($p->village_tract)){
                $observer['A']['village_tract'] = $p->village_tract;
            }elseif(isset($p->village_tract_english)){
                $observer['A']['village_tract'] = $p->village_tract_english;
                $observer['B']['village_tract'] = $p->village_tract_english;
            }else{
                
            }
            if(isset($p->township)){
                $observer['A']['township'] = $p->township;
            }elseif(isset($p->township_english)){
                $observer['A']['township'] = $p->township_english;
                $observer['B']['township'] = $p->township_english;
            }else{
                
            }
            if(isset($p->district)){
                $observer['A']['district'] = $p->district;
            }elseif(isset($p->district_english)){
                $observer['A']['district'] = $p->district_english;
                $observer['B']['district'] = $p->district_english;
            }else{
                
            }
            if(isset($p->state)){
                $observer['A']['state'] = $p->state;
            }elseif(isset($p->state_english)){
                $observer['A']['state'] = $p->state_english;
                $observer['B']['state'] = $p->state_english;
            }else{
                
            }
            if(isset($p->spot_checker_id)){
                $spot_checker['participant_id'] = $p->spot_checker_id;
                $spot_checker['name'] = 'No Name';
            }
            
            if(isset($observer)){
                foreach($observer as $key => $person){
                    $person['participant_id'] = $person['pcode'].$key;
                    $place = $this->pcode->findOrThrowException($person['pcode'].'-'.$org);
                    $participant = $this->create($person, $place, $role, $org );
                }
                    
            }elseif(isset($supervisor)){
                    $place = $this->pcode->findOrThrowException($supervisor['pcode'].'-'.$org);
                    $participant = $this->create($supervisor, $place, $role, $org );
                    
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
	private function createParticipantStub($input, $pcode_id, $org)
	{   
		$attributes['participant_id'] = (array_key_exists('participant_id', $input)? $input['participant_id']:null);
                $participant = Participant::firstOrNew(['participant_id' => $attributes['participant_id'], 'pcode_id' => $pcode_id, 'org_id' => $org]);
                
		$participant->name = (array_key_exists('name', $input)? $input['name']:'No Name');
                $participant->avatar = (array_key_exists('avatar', $input)? $input['avatar']:'');
		$participant->email = (array_key_exists('email', $input)? $input['email']:'No Email');
		$participant->nrc_id = (array_key_exists('nrc_id', $input)? $input['nrc_id']:null);
                $participant->dob = (array_key_exists('dob', $input)? $input['dob']:'');
                $participant->base = (array_key_exists('base', $input)? $input['base']:'');
                $participant->gender = (array_key_exists('gender', $input)? $input['gender']:'Not Specified');
                $participant->participant_id = (array_key_exists('participant_id', $input)? $input['participant_id']:null);
                
		return $participant;
	}
        
        public function cliImport($file, $org, $role) {
            
            $excel = Excel::filter('chunk')->load($file, 'UTF-8')->chunk(50, function($participant) use ($file, $org, $role){
                            //$this->makeNestedSetArray($participant, $org, $role);
                            $participant->each(function($row) use ($role, $org) {
                                $this->arrayToNestedSet($row, $org, $role);
                            });
                        });
             //dd($excel)->all();
            //$excel = Excel::load($file, 'UTF-8')->all();
            //$this->makeNestedSetArray($excel, $org, $role);
        }
}