<?php namespace App\Repositories\Backend\PLocation;

/**
 * Interface LocationContract
 * @package App\Repositories\Location
 */
interface PLocationContract {

	/**
	 * @param $id
	 * @param bool $withRoles
	 * @return mixed
	 */
	public function findOrThrowException($id);
        
        public function getLocationByPcode($pcode, $org);
        
        public function getLocationByUecCode($uecCode, $org);
        
        /**
         * 
         * 
         */
        public function getCountry($country);
        
        public function getState($string);
        
        public function getDistrict($string);
        
        public function getTownship($string);
        
        public function getVtrack($string);
        
        public function getCountryScope($country, $order, $sort);
        
        public function getStatesScope($country, $order, $sort);
        
        public function getDistrictsScope($country, $order, $sort);
        
        public function getTownshipsScope($country, $order, $sort);
        
        public function getVtractsScope($country, $order, $sort);
        
        public function getVillagesScope($country, $order, $sort);
        
        public function findVillagesById($id, $order, $sort);
        
        public function findVTracksById($id, $order, $sort);
        
        public function findTownshipsById($id, $order, $sort);
        
        public function findDistrictsById($id, $order, $sort);
        
        public function findStatesById($id, $order, $sort);
        
        public function findSiblingOfStatesById($id, $order, $sort);

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
	public function getLocationsPaginatedTable($per_page, $org_id, $withOrg, $order_by = 'id', $sort = 'asc');
        
        public function setPcode($org, $location, $level);
        
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
	public function create($input, $org_id, $location_id);

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
        
        public function buildTree($array);
        
        public function makeTree($array);
        
        public function cliImport($file, $org, $level);
}