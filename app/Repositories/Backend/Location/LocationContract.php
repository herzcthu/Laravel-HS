<?php namespace App\Repositories\Backend\Location;

/**
 * Interface LocationContract
 * @package App\Repositories\Location
 */
interface LocationContract {

	/**
	 * @param $id
	 * @param bool $withRoles
	 * @return mixed
	 */
	public function findOrThrowException($id, $withPcode);
        
        /**
         * 
         * 
         */
        public function searchOnlyName($q, $country, $column, $order, $sort);
        
        public function getCountry($country);
        
        public function getState($string);
        
        public function getDistrict($string);
        
        public function getTownship($string);
        
        public function getVtrack($string);
        
        public function getCountryScope($country, $hasParticipant = false);
        
        public function getStatesScope($country, $hasParticipant = false);
        
        public function getDistrictsScope($country, $hasParticipant = false);
        
        public function getTownshipsScope($country, $hasParticipant = false);
        
        public function getVtracksScope($country, $hasParticipant = false);
        
        public function getVillagesScope($country, $hasParticipant = false);
        
        public function findVillagesById($id, $order, $sort);
        
        public function findVTracksById($id, $order, $sort);
        
        public function findTownshipsById($id, $order, $sort);
        
        public function findDistrictsById($id, $order, $sort);
        
        //public function findDistrictsByType($type);
        
        public function findStatesById($id, $order, $sort);
        
        public function findSiblingOfStatesById($id, $order, $sort);
        
        public function findSiblingByName($name, $type);
        
        public function findParentsByNodeName($name);
        
        public function getVillageTreeByNodesNames($village, Array $names);
        
        public function getChildrenByName($name, $type, $hasParticipant);
        
        public function getLeavesByName($name, $type, $hasParticipant);
        
        public function getRootByName($name, $type, $hasParticipant);

        /**
	 * @param $per_page
	 * @param string $order_by
	 * @param string $sort
	 * @param $status
	 * @return mixed
	 */
	public function getLocationsPaginated($per_page, $withOwner = false, $status = 1, $order_by = 'id', $sort = 'asc');
        
        /**
	 * @param $per_page
	 * @param string $order_by
	 * @param string $sort
	 * @param $status
	 * @return mixed
	 */
	public function getLocationsPaginatedTable($per_page, $type, $order_by = 'id', $sort = 'asc');
        
        public function importLocations($excel);
        
        /**
	 * @param $per_page
	 * @param string $order_by
	 * @param string $sort
	 * @param $status
	 * @return mixed
	 */
	public function searchLocations($queue, $search_by, $order_by = 'name', $sort = 'asc');

	/**
	 * @param $per_page
	 * @return \Illuminate\Pagination\Paginator
	 */
	public function getDeletedLocationsPaginated($per_page);

	/**
	 * @param string $order_by
	 * @param string $sort
	 * @return mixed
	 */
	public function getAllLocations($order_by = 'id', $sort = 'asc');

	/**
	 * @param $input
	 * @param $roles
	 * @return mixed
	 */
	public function create($location = []);

	/**
	 * @param $id
	 * @param $input
	 * @param $roles
	 * @return mixed
	 */
	public function update($id, $input);

	/**
	 * @param $id
	 * @return mixed
	 */
	public function destroy($id);

	/**
	 * @param $id
	 * @return mixed
	 */
	public function delete($id);

	/**
	 * @param $id
	 * @return mixed
	 */
	public function restore($id);

	/**
	 * @param $id
	 * @param $status
	 * @return mixed
	 */
	public function mark($id, $status);

        public function merge_excel_import($exceldata);
        
        public function arrayToNestedSet($country, $array);
        
        public function buildTree($array);
        
        public function makeTree($array);
}