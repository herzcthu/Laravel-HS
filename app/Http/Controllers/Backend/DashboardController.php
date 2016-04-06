<?php namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Organization\OrganizationContract;
use App\Repositories\Backend\PLocation\PLocationContract;
use App\Repositories\Backend\Project\ProjectContract;

/**
 * Class DashboardController
 * @package App\Http\Controllers\Backend
 */
class DashboardController extends Controller {
    
        protected $organizations;

        protected $projects;

        protected $plocations;

        public function __construct(
         OrganizationContract $organizations,
         ProjectContract $projects,
         PLocationContract $plocations
         ) 
        {
            $this->organizations = $organizations;
            $this->projects = $projects;
            $this->plocations = $plocations;
        }
	/**
	 * @return \Illuminate\View\View
	 */
	public function index()
	{
            if(auth()->user()->can('manage_organization')){
                $orgs = $this->organizations->getAllOrganizations('name', 'asc');
            }else{
                $orgs[0] = auth()->user()->organization;
            }
            return view('backend.dashboard')
                   ->withOrgs($orgs);
	}
}