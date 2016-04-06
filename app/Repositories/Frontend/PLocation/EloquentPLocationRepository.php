<?php namespace App\Repositories\Frontend\PLocation;

use App\Exceptions\GeneralException;
use App\PLocation;
use App\Repositories\Frontend\Location\LocationContract;
use App\Repositories\Frontend\Organization\OrganizationContract;
use App\Repositories\Frontend\Participant\Role\RoleRepositoryContract;
use App\Repositories\Frontend\Auth\AuthenticationContract;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Class EloquentLocationRepository
 * @package App\Repositories\Location
 */
class EloquentPLocationRepository implements PLocationContract {
    
    protected $locations;
    
    protected $organizations;
    
    protected $proles;

    /**
	 * @param UserRepositoryContract $role
	 * @param AuthenticationContract $auth
	 */
	public function __construct(LocationContract $locations, 
                                    OrganizationContract $organizations,
                                    RoleRepositoryContract $proles) {
            $this->locations = $locations;
            $this->organizations = $organizations;
            $this->proles = $proles;
	}

	/**
	 * @param $id
	 * @param bool $withUsers
	 * @return mixed
	 * @throws GeneralException
	 */
	public function findOrThrowException($id) {
                $location = PLocation::find($id);                        

                if (! is_null($location)) return $location;

		throw new GeneralException('That location does not exist.');
	}
        
        public function getLocationByPcode($pcode, $org){
            $plocation = PLocation::where('pcode',$pcode)->where('org_id', $org)->first();
            if($plocation)               
                return $this->findOrThrowException($plocation->id);
            
            throw new GeneralException('That location does not exist.');
        }
        
        public function getLocationByUecCode($uecCode, $org){
            $plocation = PLocation::where('uec_code',$uecCode)->where('org_id', $org)->first();
            if($plocation)               
                return $this->findOrThrowException($plocation->id);
            
            throw new GeneralException('That location does not exist.');
        }
        
        public function getCountry($country) {            
            $country = PLocation::where('pcode', $country)->first();
            return $this->findOrThrowException($country->id);
        }
        
        public function getState($string){
            
        }
        
        public function getDistrict($string){
            
        }
        
        public function getTownship($string){
            
        }
        
        public function getVtrack($string){
            
        }
        
        public function getCountryScope($country, $order, $sort) {            
            $country = $this->getCountry($country);
            $states = $country->descendants()->orderBy($order, $sort);
        }

        public function getStatesScope($name, $order='village', $sort='asc') {
            
            $states = PLocation::where('state', $name)->orderBy($order,$sort);
            return $states;
        }
        
        public function getDistrictsScope($name, $order='village', $sort='asc'){
            $districts = PLocation::where('district', $name)->orderBy($order,$sort);
            return $districts;
        }
        
        public function getTownshipsScope($name, $order='village', $sort='asc'){           
            $townships = PLocation::where('township', $name)->orderBy($order,$sort);                    
            return $townships;
        }
        
        public function getVtractsScope($name, $order='village', $sort='asc'){
            $vtracts = PLocation::where('village_tract', $name)->orderBy($order,$sort);
            return $vtracts;
        }
        
        public function getVillagesScope($name, $order='village', $sort='asc'){
            $villages = PLocation::where('village', $name)->orderBy($order,$sort);
            return $villages;
        }
        
        public function findVillagesById($id, $order, $sort){
            $location = $this->findOrThrowException($id);
            if($location->type !== 'village'){
                return $location->leaves()->orderBy($order, $sort);
            }
            return $location;
        }
        
        public function findVTracksById($id, $order, $sort){
            $location = $this->findOrThrowException($id);
            if($location->type === 'village') {
                return $location->ancestors()->where('type','village_tract')->orderBy($order, $sort);
            }
            if($location->type === 'village_tract'){
                return $location;
            }
            if($location->type === 'township'){
                return $location->descendants()->limitDepth(1)->where('type','village_tract')->orderBy($order, $sort);
            }
            if($location->type === 'district'){
                return $location->descendants()->limitDepth(2)->where('type','village_tract')->orderBy($order, $sort);
            }
            if($location->type === 'state'){
                return $location->descendants()->limitDepth(3)->where('type','village_tract')->orderBy($order, $sort);
            }
        }
        
        public function findTownshipsById($id, $order, $sort){
            $location = $this->findOrThrowException($id);
            if($location->type === 'village') {
                return $location->ancestors()->where('type','township')->orderBy($order, $sort);
            }
            if($location->type === 'village_tract'){
                return $location->ancestors()->where('type','township')->orderBy($order, $sort);
            }
            if($location->type === 'township'){
                return $location;
            }
            if($location->type === 'district'){
                return $location->descendants()->limitDepth(1)->where('type','township')->orderBy($order, $sort);
            }
            if($location->type === 'state'){
                return $location->descendants()->limitDepth(2)->where('type','township')->orderBy($order, $sort);
            }
        }
        
        public function findDistrictsById($id, $order, $sort){
            $location = $this->findOrThrowException($id);
            if($location->type === 'village') {
                return $location->ancestors()->where('type','district')->orderBy($order, $sort);
            }
            if($location->type === 'village_tract'){
                return $location->ancestors()->where('type','district')->orderBy($order, $sort);
            }
            if($location->type === 'township'){
                return $location->ancestors()->where('type','district')->orderBy($order, $sort);
            }
            if($location->type === 'district'){
                return $location;
            }
            if($location->type === 'state'){
               return $location->descendants()->limitDepth(1)->where('type','district')->orderBy($order, $sort);
            }
        }
        
        public function findStatesById($id, $order, $sort){
            $location = $this->findOrThrowException($id);
            if($location->type === 'village' || $location->type === 'village_tract' || $location->type === 'township' || $location->type === 'district') {
                return $location->ancestors()->where('type','state')->orderBy($order, $sort);
            }
            
            if($location->type === 'state'){
                return $location;
            }
        }
        
        public function findSiblingOfStatesById($id, $order, $sort) {
            $state = $this->findStatesById($id, $order, $sort);
            $location = $this->findOrThrowException($id);
            if($location->type === 'states'){
                return $location->siblingsAndSelf()->orderBy($order, $sort);
            }
            if($location->type === 'district'){
                return $location->siblingsAndSelf()->orderBy($order, $sort);
            }
            if($location->type === 'township'){
                return $state->descendants()->limitDepth(2)->where('type','township')->orderBy($order, $sort);
            }
            if($location->type === 'village_tract'){
                return $state->descendants()->limitDepth(3)->where('type','village_tract')->orderBy($order, $sort);
            }
            if($location->type === 'village'){                
                return $state->first()->leaves();
            }
        }
        
	/**
	 * @param $per_page
	 * @param string $order_by
	 * @param string $sort
	 * @param int $status
	 * @return mixed
	 */
	public function getLocationsPaginated($per_page, $withUsers = false, $status = 0, $order_by = 'id', $sort = 'asc') {
            
            if ($withUsers)	
            {
                $auth_id = access()->user()->id;
                
                return PLocation::where('owner_id', $auth_id)->orderBy($order_by, $sort)->paginate($per_page);
            }else{
                return PLocation::orderBy($order_by, $sort)->paginate($per_page);
            }
                
	}
        
        /**
	 * @param $per_page
	 * @param string $order_by
	 * @param string $sort
	 * @param int $status
	 * @return mixed
	 */
	public function getLocationsPaginatedTable($per_page = 15, $org_id = false, $withOrg = false, $order_by = 'pcode', $sort = 'asc') {
                $order_by = ((null !== Input::get('field'))? Input::get('field'):$order_by);
                $sort = ((null !== Input::get('sort'))? Input::get('sort'):$sort);
                if($withOrg) {
                    $plocation = PLocation::with('organization')->orderBy($order_by, $sort);
                } else {
                   $plocation = PLocation::orderBy($order_by, $sort);  
                } 
                if($org_id){
                    return $plocation->where('org_id', $org_id)->paginate($per_page);
                }else{
                    return $plocation->paginate($per_page);
                }
	}
        
        public function setPcode($location, $org, $level) {
            $organization = $this->organizations->findOrThrowException($org);
            $prole = $this->proles->findOrThrowException($level);
            if(isset($location->pcode)){
                $location->pcode = (string) $location->pcode;
            }elseif(isset($location->no)){
                $location->pcode = (string) $location->no;
            }elseif(isset($location->custom_location_code)){
                $location->pcode = (string) $location->custom_location_code;
            }else{
                throw new GeneralException('No valid location code found! Check your upload file!');
            }
                if(!empty($location->pcode)){
                    $trees = $this->makeTreeFromInput($location, $location->pcode, $org);

                    if(isset($location->pcode)){
                        $trees->pcode = (string) $location->pcode;
                    }
                    if(isset($location->uec_code)){
                        $trees->uec_code = (string) $location->uec_code;
                    }
                    $trees->proles()->associate($prole);
                    $trees->organization()->associate($organization);
                    $trees->save();
                }else{
                    throw new GeneralException('Location Code invalid!');
                }
        }
        /**
	 * @param $per_page
	 * @param string $order_by
	 * @param string $sort
	 * @param int $status
	 * @return mixed
	 */
	public function searchLocations($q, $search_by, $order_by = 'name', $sort = 'asc') {
            $order_by = ((null !== Input::get('field'))? Input::get('field'):$order_by);
            $sort = ((null !== Input::get('sort'))? Input::get('sort'):$sort);
            
            return Location::where('type',$search_by)->search($q)->orderBy($order_by, $sort)->get();
            
	}

	/**
	 * @param $per_page
	 * @return Paginator
	 */
	public function getDeletedLocationsPaginated($per_page) {
		return Location::onlyTrashed()->paginate($per_page);
	}

	/**
	 * @param string $order_by
	 * @param string $sort
	 * @return mixed
	 */
	public function getAllLocations($order_by = 'pcode', $sort = 'asc') {
		return PLocation::orderBy($order_by, $sort)->get();
	}

	/**
	 * @param $input
	 * @param $users
	 * @param $permissions
	 * @return bool
	 * @throws GeneralException
	 * @throws LocationNeedsUsersException
	 */
	public function create($input, $org_id, $location_id) {
            
		$location = $this->locations->findOrThrowException($location_id['location_id']);
                
                $organization = $this->organizations->findOrThrowException($org_id['org_id']);
                
                $plocation = $this->createLocationStub($input, $organization->id);
                
                $plocation->location()->associate($location);
                
                $plocation->organization()->associate($organization);
                
                $plocation = $plocation->save();
                
		if ($plocation) {
			
			return $plocation;
		}

		throw new GeneralException('There was a problem creating this media. Please try again.');
	}
        
        /**
	 * @param $id
	 * @param $input
	 * @param $users
	 * @return bool
	 * @throws GeneralException
	 */
	public function update($id, $input) {
		$media = $this->findOrThrowException($id);
		//$this->checkLocationByEmail($input, $media);
                //dd(\Illuminate\Support\Facades\Input::file());
                
                
		if ($media->update($input)) {
                      if ($file) {
                       // $media->saveLocation($file, 'profile_picture');
                      }

			//For whatever reason this just wont work in the above call, so a second is needed for now
			$media->status = isset($input['status']) ? 1 : 0;
			$media->confirmed = isset($input['confirmed']) ? 1 : 0;
			$media->save();

			$this->checkLocationUsersCount($users);
			$this->flushUsers($users, $media);
			$this->flushPermissions($permissions, $media);

			return true;
		}

		throw new GeneralException('There was a problem updating this media. Please try again.');
	}

	
	/**
	 * @param $id
	 * @return bool
	 * @throws GeneralException
	 */
	public function destroy($id) {
		if (auth()->id() == $id)
			throw new GeneralException("You can not delete yourself.");

		$media = $this->findOrThrowException($id);
		if ($media->delete())
			return true;

		throw new GeneralException("There was a problem deleting this media. Please try again.");
	}

	/**
	 * @param $id
	 * @return boolean|null
	 * @throws GeneralException
	 */
	public function delete($id) {
		$media = $this->findOrThrowException($id, true);

		//Detach all users & permissions
		$media->detachUsers($media->users);
		$media->detachPermissions($media->permissions);

		try {
			$media->forceDelete();
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
		$media = $this->findOrThrowException($id);

		if ($media->restore())
			return true;

		throw new GeneralException("There was a problem restoring this media. Please try again.");
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

		$media = $this->findOrThrowException($id);
		$media->status = $status;

		if ($media->save())
			return true;

		throw new GeneralException("There was a problem updating this media. Please try again.");
	}

	/**
	 * Check to make sure at lease one role is being applied or deactivate media
	 * @param $media
	 * @param $users
	 * @throws LocationNeedsUsersException
	 */
	private function validateUserAmount($media, $users) {
		//Validate that there's at least one role chosen, placing this here so
		//at lease the media can be updated first, if this fails the users will be
		//kept the same as before the media was updated
		if (count($users) == 0) {
			//Deactivate media
			$media->status = 0;
			$media->save();

			$exception = new LocationNeedsUsersException();
			$exception->setValidationErrors('You must choose at lease one role. Location has been created but deactivated.');

			//Grab the media id in the controller
			$exception->setLocationID($media->id);
			throw $exception;
		}
	}

	

	/**
	 * @param $users
	 * @param $media
	 */
	private function flushUsers($users, $media)
	{
		//Flush users out, then add array of new ones
		$media->detachUsers($media->users);
		$media->attachUsers($users['owner_id']);
	}
	

	/**
	 * @param $users
	 * @throws GeneralException
	 */
	private function checkLocationUsersCount($users)
	{
		//Location Updated, Update Users
		//Validate that there's at least one role chosen
		if (count($users['owner_id']) == 0)
			throw new GeneralException('You must choose at least one role.');
	}

	/**
	 * @param $input
	 * @return mixed
	 */
	private function createLocationStub($request, $org_id)
	{
		$location = PLocation::firstOrNew(['primaryid' => $request['pcode'].'-'.$org_id]);
                $location->pcode = $request['pcode'];
                if(array_key_exists('uec_code', $request)){
                    $location->uec_code = $request['uec_code'];
                }
                
		return $location;
	}
        
        private function createLocationStubFromImport($excel) {
            $location = new Location;
            
            
            return $location;
        }
        
        public function merge_excel_import($exceldata)
        {
            //dd($exceldata);
            foreach ($exceldata as $key => $file) {
                //$row = $file->all();
                //dd($key);
                foreach($file as $row){
                    
                    $vpcode = (string) $row->village_pcode;

                    /**
                    $children[] =  ['name' => $row->state_region, 'pcode' => $row->sr_pcode, 'type' => 'state',
                                'children' => [['name' => $row->district, 'pcode' => $row->d_pcode, 'type' => 'district',
                                                'children' => [['name' => $row->township, 'pcode' => $row->ts_pcode, 'type' => 'township',
                                                    'children' => [['name' => $row->village_tract, 'pcode' => $row->vt_pcode, 'type' => 'village_tract',
                                                        'children' => [['name' => $row->village, 'pcode' => $vpcode, 'type' => 'village',
                                                            'alt_name' => $row->alternate_vlg_name_eng,'mya_name' => $row->village_mya_mmr3,'alt_mya_name' => $row->alternate_vll_name_mya,'long' => $row->long, 'lat' => $row->lat
                                                        ]]                                                                
                                                    ]]                                                            
                                                ]]                                            
                                ]]                                    

                    ];
                     * 
                     */

                    $village[$row->vt_pcode][$vpcode] = ['name' => $row->village, 'pcode' => $vpcode, 'type' => 'village',
                                                            'alt_name' => $row->alternate_vlg_name_eng,'mya_name' => $row->village_mya_mmr3,'alt_mya_name' => $row->alternate_vll_name_mya,'long' => $row->long, 'lat' => $row->lat
                                                        ];
                    $vtrack[$row->ts_pcode][$row->vt_pcode] = ['name' => $row->village_tract, 'pcode' => $row->vt_pcode, 'type' => 'village_tract',
                                                        'children' => $village[$row->vt_pcode] ];
                    $tsp[$row->d_pcode][$row->ts_pcode] = ['name' => $row->township, 'pcode' => $row->ts_pcode, 'type' => 'township',
                                                    'children' => $vtrack[$row->ts_pcode] ];
                    $district[$row->sr_pcode][$row->d_pcode] = ['name' => $row->district, 'pcode' => $row->d_pcode, 'type' => 'district',
                                                'children' => $tsp[$row->d_pcode] ];
                    $state[$row->sr_pcode] = ['name' => $row->state_region, 'pcode' => $row->sr_pcode, 'type' => 'state',
                                'children' => $district[$row->sr_pcode] ];
                }
                
            }
            
            return $state;

        }
        
        public function buildTree($array){
            return Location::buildTree($array);
        }
        
        public function makeTree($array){
            return Location::makeTree($array);
        }

        private function makeTreeFromInput($location, $pcode, $org_id) {
            $trees = PLocation::firstOrNew(['primaryid' => $pcode.'-'.$org_id]);//dd($location);
            $trees->primaryid = $pcode.'-'.$org_id;
            if(isset($location->stateregion_english)){
                    $trees->state = $location->stateregion_english;
                }elseif(isset($location->state_region)){
                    $trees->state = $location->state_region;
                }elseif(isset($location->stateregion)){
                    $trees->state = $location->stateregion;
                }else{

                }
                
                if(isset($location->district_english)){
                    $trees->district = $location->district_english;
                }elseif(isset($location->district)){
                    $trees->district = $location->district;
                }elseif(isset($location->district_burmese)){
                    $trees->district = $location->district_burmese;
                }else{

                }
                
                if(isset($location->township_english)){
                    $trees->township = $location->township_english;
                }elseif(isset($location->township)){
                    $trees->township = $location->township;
                }elseif(isset($location->township_burmese)){
                    $trees->township = $location->township_burmese;
                }else{

                }
                if(isset($location->village_tract)){
                    $trees->village_tract = $location->village_tract;
                }elseif(isset($location->village_tract_burmese)){
                    $trees->village_tract = $location->village_tract_burmese;
                }elseif(isset($location->village_tracttown)){
                    $trees->village_tract = $location->village_tracttown;
                }
                else{

                }
                if(isset($location->villageward)){
                    $trees->village = $location->villageward;
                }elseif(isset($location->village_mya_mmr3)){
                    $trees->village = $location->village_mya_mmr3;
                }elseif(isset($location->polling_station_location_burmese)){
                    $trees->village = $location->polling_station_location_burmese;
                }else{

                }
                return $trees;
        }
        public function cliImport($file, $org, $level) {
            $excel = Excel::filter('chunk')->load($file, 'UTF-8')->chunk(100, function($locations) use ($file, $org, $level){
                            // Loop through all rows
                            //dd($location);
                            $locations->each(function($row) use ($org, $level) {
                                //$this->plocations->setPcode($org, $locations);
                                $this->setPcode($row, $org, $level);
                            });
                            
                        });
        }
}