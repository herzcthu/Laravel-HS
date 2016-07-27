<?php namespace App\Http\Requests\Backend\Project\Result;

use App\Http\Requests\Request;

class UpdateResultRequest extends Request {

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
		return [
			'answer'	=>  'required',	
                        'validator_id' => 'required',
                        'project_id' => 'required',
                        'org_id' => 'required'
		];
	}
        
        public function messages() {
            
            return [
              'validator_id.required' => 'Validation Code has error!',
              'project_id.required' => 'You are doing something wrong. Go back to projects list and try again!',
              'org_id.required' => 'You are doing something wrong. Go back to projects list and try again!'
            ];            
        }
}