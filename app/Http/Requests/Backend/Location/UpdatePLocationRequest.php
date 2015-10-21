<?php namespace App\Http\Requests\Backend\Location;

use App\Http\Requests\Request;

class UpdatePLocationRequest extends Request {

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
			'location' => 'required',
                        'location_id' => 'required',
		];
	}
        
        public function messages() {
            
            return [
              'location_id.required' => 'Location field is required.' 
            ];            
        }
}