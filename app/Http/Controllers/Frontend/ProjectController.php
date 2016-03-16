<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Repositories\Frontend\Project\ProjectContract;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProjectController extends Controller
{
    protected $projects;
    
    public function __construct(
            ProjectContract $projects
            ) {
        $this->projects = $projects;
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
        return view('frontend.project.index')
			->withProjects($this->projects->getProjectsPaginated(config('access.projects.default_per_page')));
    }
}
