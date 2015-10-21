<?php namespace App\Http\Requests\Backend\Project;

use App\Http\Requests\Request;

class UpdateProjectRequest extends Request {

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
            $project = $this->route('project');
		return [
			'name'	=>  'required|unique:projects,name,' . $project->id,
                        'project' => '',
                        'organization' => 'required',
		];
	}
}