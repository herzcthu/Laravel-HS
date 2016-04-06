<?php namespace App\Repositories\Backend\Location;

use App\Exceptions\GeneralException;
use App\Location;
use App\Repositories\Backend\Organization\OrganizationContract;
use App\Repositories\Frontend\Auth\AuthenticationContract;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Class EloquentLocationRepository
 * @package App\Repositories\Location
 */
class EloquentLocationRepository implements LocationContract {

	

	/**
	 * @param UserRepositoryContract $role
	 * @param AuthenticationContract $auth
	 */
	public function __construct() {
            
	}

	/**
	 * @param $id
	 * @param bool $withUsers
	 * @return mixed
	 * @throws GeneralException
	 */
	public function findOrThrowException($id, $hasPcode = false) {
            if(empty($id)){
                throw new GeneralException("Location id is empty.");
            }
            if ($hasPcode)
                $location = Location::has('pcode')->find($id);
            else
                $location = Location::find($id);

            if (! is_null($location)) return $location;

            throw new GeneralException("That location with id $id does not exist.");
	}
        //Nothing return if location not found.
        public function getLocationByPcode($pcode) {            
            $location = Location::where('pcode', $pcode)->first();
            if (! is_null($location)){
                return $this->findOrThrowException($location->id);
            }
        }
        
        public function getCountry($country) {            
            $country = Location::where('name', $country)->where('type', 'country');
            
            return $country;
        }
        
        public function getState($string){
            $state = Location::where('name', $state)->where('type', 'state');
            
            return $state;
        }
        
        public function getDistrict($string){
            $district = Location::where('name', $district)->where('type', 'district');
            
            return $district;
        }
        
        public function getTownship($string){
            $township = Location::where('name', $township)->where('type', 'township');
            
            return $township;
        }
        
        public function getVtrack($string){
            $village_tract = Location::where('name', $village_tract)->where('type', 'village_tract');
            
            return $village_tract;
        }
        
        public function getVillage($string){
            $village = Location::where('name', $village)->where('type', 'village');
            
            return $village;
        }
        
        public function getCountryScope($country, $hasParticipant = false){
            
            $country = $this->getLocationByPcode($country);
            
            if($hasParticipant){
                return $country->descendants()->has('participants');
            }else{
                return $country->descendants();
            }
        }

        public function getStatesScope($country, $hasParticipant = false){
            
            $country = $this->getLocationByPcode($country);
            
            if($hasParticipant){
                return $country->immediateDescendants()->has('participants');
            }else{
                return $country->immediateDescendants();
            }
        }
        
        public function getDistrictsScope($country, $hasParticipant = false){
            
            $country = $this->getLocationByPcode($country);
            
            if($hasParticipant){
                return $country->descendants()->limitDepth(2)->where('type', 'district')->has('participants');
            }else{
                return $country->descendants()->limitDepth(2)->where('type', 'district');
            }
        }
        
        public function getTownshipsScope($country, $hasParticipant = false){
            
            $country = $this->getLocationByPcode($country);
            
            if($hasParticipant){
                return $country->descendants()->limitDepth(3)->where('type', 'township')->has('participants');
            }else{
                return $country->descendants()->limitDepth(3)->where('type', 'township');
            }               
        }
        
        public function getVtracksScope($country, $hasParticipant = false){
            
            $country = $this->getLocationByPcode($country);
            
            if($hasParticipant){
                return $country->descendants()->limitDepth(4)->where('type', 'village_tract')->has('participants');
            }else{
                return $country->descendants()->limitDepth(4)->where('type', 'village_tract');
            }
        }
        
        public function getVillagesScope($country, $hasParticipant = false){
            
            $country = $this->getLocationByPcode($country);
            
            if($hasParticipant){
                return $country->leaves()->has('participants');
            }else{
                return $country->leaves();
            }          
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
        
        public function findSiblingByName($name, $type) {
            $self = Location::whereName($name)->whereType($type)->first();
            return $self->siblingsAndSelf();
        }
        
        /**
         * return array of collection with the same name
         * @param string $name
         * @return array
         */
        public function findParentsByNodeName($name){
            if (strlen($name) != strlen(utf8_decode($name))) {
            // $str uses multi-byte chars (isn't English)
                $column = 'mya_name';
            } else {
                // $str is ASCII (probably English)
                $column = 'name';
            }
            $nodes = Location::where($column, $name)->get();
            $tree = [];
            foreach($nodes as $node){
                $tree[] = $node->getAncestorsAndSelf();
            }
            return $tree;
        }
        
        /**
         * 
         * @param array $names
         */
        public function getVillageTreeByNodesNames($village, Array $names){
            /**
             * $locations is array of village object which has same name and its ancestors
             */
            $locations = $this->findParentsByNodeName($village);
            $place = [];
            $villageTree = [];
            /**
             * iterate $locations to $key and $location
             * integer $key
             * object $location
             */
            foreach ($locations as $key => $location){
                /**
                 * $location has many collections with different in column 'type'
                 * 
                 */
                //dd($location);
                foreach ($names as $type => $name){
                    if (strlen($name) != strlen(utf8_decode($name))) {
                    // $str uses multi-byte chars (isn't English)
                        $column = 'mya_name';
                    } else {
                        // $str is ASCII (probably English)
                        $column = 'name';
                    }
                    $place[$key][$type] = $location->where('type', $type)->where($column, $name)->first();
                }
                //if(!in_array(null, array_values($place[$key]))){
                   $villageTree = $place[$key];
                //}else{
                  //  $villageTree = [];
                //}
                
            }
            return $villageTree;
        }
        
        public function getChildrenByName($name, $type, $hasParticipant = false){
            if($type != 'village' || $type != 'village_tract'){
                if($hasParticipant){
                    return Location::whereName($name)->whereType($type)->first()->descendantsAndSelf()->has('participants');
                }else{
                    return Location::whereName($name)->whereType($type)->first()->descendantsAndSelf();
                }
            }
            throw new GeneralException('Check location type.');
        }
        
        public function getLeavesByName($name, $type, $hasParticipant = false){
            if($type != 'village' || $type != 'village_tract'){
                if($hasParticipant){
                    return Location::whereName($name)->whereType($type)->first()->leaves()->has('participants');
                }else{
                    return Location::whereName($name)->whereType($type)->first()->leaves();
                }
            }
            throw new GeneralException('Check location type.');
        }
        
        public function getRootByName($name, $type, $hasParticipant = false){
            
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
                
                return Location::where('owner_id', $auth_id)->orderBy($order_by, $sort)->paginate($per_page);
            }else{
                return Location::orderBy($order_by, $sort)->paginate($per_page);
            }
                
	}
        
        /**
	 * @param $per_page
	 * @param string $order_by
	 * @param string $sort
	 * @param int $status
	 * @return mixed
	 */
	public function getLocationsPaginatedTable($per_page, $type, $order_by = 'id', $sort = 'asc') {
                $order_by = ((null !== Input::get('field'))? Input::get('field'):$order_by);
                $sort = ((null !== Input::get('sort'))? Input::get('sort'):$sort);
                return Location::where('type', $type)->orderBy($order_by, $sort)->paginate($per_page);      		
	}
        
        public function importLocations($excel) {
            
        
               Excel::load($excel, function($rows) {
                                       
                        $this->rows = $rows->each(function($row) {});
                        
                });
               $nested_set = $this->merge_excel_import($this->rows);
               $parent = Location::where('pcode', '=', 'MMR')->first();
                //dd($parent->location);
                $imported = $parent->makeTree($nested_set); // => true
                //$node = Location::create($children);
                if($imported){ 

                }else{
                    throw new GeneralException('Error: there is an error importing.');                                 
                }
        }
        /**
	 * @param $per_page
	 * @param string $order_by
	 * @param string $sort
	 * @param int $status
	 * @return mixed
	 */
	public function searchOnlyName($q, $country, $column, $withPcode = false, $withParticipants = false, $order_by = 'name', $sort = 'asc') {
            $order_by = ((null !== Input::get('field'))? Input::get('field'):$order_by);
            $sort = ((null !== Input::get('sort'))? Input::get('sort'):$sort);
            $country = $this->getCountryScope($country, $withPcode, $withParticipants);
            
            return $country->select('id', $column)->where($column,'like', $q.'%')->orderBy($order_by, $sort);
            
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
            
           // return Location::where('type',$search_by)->search($q, 30, true)->orderBy($order_by, $sort);
             return Location::where('type',$search_by)->where('name', 'like', $q)->orderBy($order_by, $sort);
            
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
	public function getAllLocations($order_by = 'id', $sort = 'asc') {
		return Location::orderBy($order_by, $sort)->get();
	}

	/**
	 * @param $input
	 * @param $users
	 * @param $permissions
	 * @return bool
	 * @throws GeneralException
	 * @throws LocationNeedsUsersException
	 */
	public function create($location = []) {
            
		$location = Location::create($location);
                if($location){
                    return $location;
                }
		throw new GeneralException('There was a problem creating location. Please try again.');
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
	private function createLocationStub($request)
	{
		$media = new Location;
                
                if ($request->hasFile('file')) {
                    $mime = $request->file('file')->getMimeType();
                    
                    if(substr($mime, 0, 5) == 'image') {
                        $data = $this->image_upload->upload($request->file('file'));
                    }
                    else {
                        die('this is not image');  
                    }
                                  
                }
                              
		$media->file = json_encode($data);
                
                $media->filename = $data['original_filename'];
                $media->filedir = $data['original_filedir'];
		$media->owner_id = $request['owner_id'];
                ///$media->save();
		return $media;
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
        
        public function arrayToNestedSet($pcode, $array){
            foreach($array as $row)
            {
                $vpcode = (string) $row->village_pcode;
                $root = $this->getLocationByPcode($pcode);
                if(isset($row->sr_pcode) && $root->type == 'country'){
                    $state = $this->getLocationByPcode($row->sr_pcode);
                    if(!$state instanceof Location){
                        $state_array = ['name' => $row->state_region, 'pcode' => $row->sr_pcode, 'type' => 'state'];
                        $state = $this->create($state_array);
                        $state->makeChildOf($root);
                    }
                }else{
                    $state = $root;
                }
                
                if(isset($row->d_pcode) && $state->type == 'state'){
                    $district = $this->getLocationByPcode($row->d_pcode);
                    if(!$district instanceof Location){
                        $district_array = ['name' => $row->district, 'pcode' => $row->d_pcode, 'type' => 'district'];
                        $district = $this->create($district_array);
                        $district->makeChildOf($state);
                    }
                }else{
                    $district = $root;
                }
                
                if(isset($row->ts_pcode) && $district->type == 'district'){
                    $tsp = $this->getLocationByPcode($row->ts_pcode);
                    if(!$tsp instanceof Location){
                        $tsp_array = ['name' => $row->township, 'pcode' => $row->ts_pcode, 'type' => 'township'];
                        $tsp = $this->create($tsp_array);
                        $tsp->makeChildOf($district);
                    }
                }else{
                    $tsp = $root;
                }
                
                if(isset($row->vt_pcode) && $tsp->type == 'township'){
                    $vtrack = $this->getLocationByPcode($row->vt_pcode);
                    if(!$vtrack instanceof Location){
                        $vtrack_array = ['name' => $row->village_tract, 'pcode' => $row->vt_pcode, 'type' => 'village_tract'];
                        $vtrack = $this->create($vtrack_array);
                        $vtrack->makeChildOf($tsp);
                    }
                }else{
                    $vtrack = $root;
                }
                
                if(isset($vpcode) && $vtrack->type == 'village_tract'){
                    $village = $this->getLocationByPcode($vpcode);
                    if(!$village instanceof Location){
                        $v_array = [
                                    'name' => $row->village, 'pcode' => $vpcode, 'type' => 'village',
                                    'alt_name' => $row->alternate_vlg_name_eng,'mya_name' => $row->village_mya_mmr3,
                                    'alt_mya_name' => $row->alternate_vll_name_mya,'long' => $row->long, 'lat' => $row->lat
                                    ];
                        $village = $this->create($v_array);
                        $village->makeChildOf($vtrack);
                    }
                }
            }
        }


        public function buildTree($array){
            return Location::buildTree($array);
        }
        
        public function makeTree($array){
            return Location::makeTree($array);
        }
}