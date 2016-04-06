<?php namespace App\Http\Requests\Backend\Location;

use App\Http\Requests\Request;

class CreatePLocationRequest extends Request {

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
                        'pcode' => 'required|string',
                        'uec_code' => 'required|string'
		];
	}
        
        public function messages() {
            
            return [
              'location_id.required' => 'The location field is required.' 
            ];            
        }
        
}