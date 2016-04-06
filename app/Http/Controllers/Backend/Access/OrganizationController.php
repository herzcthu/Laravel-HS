<?php

namespace App\Http\Controllers\Backend\Access;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Repositories\Backend\Organization\OrganizationContract;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OrganizationController extends Controller
{
    /**
	 * @var OrganizationRepositoryContract
	 */
	protected $organizations;

	/**
	 * @param OrganizationRepositoryContract $organizations
	 * @param PermissionRepositoryContract $permissions
	 */
	public function __construct(
		OrganizationContract $organizations) {
		$this->organizations = $organizations;
	}
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
        return view('backend.access.organizations.index')
			->withOrganizations($this->organizations->getOrganizationsPaginated(50));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
        return view('backend.access.organizations.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $this->organizations->create($request->all());
		return redirect()->route('admin.access.organizations.index')->withFlashSuccess('The organization was successfully created.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($organization)
    {
        //
        return view('backend.access.organizations.edit')
                    ->withOrganization($organization);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $organization)
    {
        $this->organizations->update($organization, $request->all());
		return redirect()->route('admin.access.organizations.index')->withFlashSuccess('The organization was successfully created.');
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
}
