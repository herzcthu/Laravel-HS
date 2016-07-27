<?php namespace App\Http\Requests\Backend\Location;

use App\Http\Requests\Request;

class CreateLocationRequest extends Request {

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
			'village' => 'required',
                        'pcode' => 'required',
                        'org_id' => 'required'
		];
	}
        
        public function messages() {
            
            return [
              'pcode.required' => 'Custom Location Code field is required.',
              'org_id.required' => 'Organization field is required.' 
            ];            
        }
        
}