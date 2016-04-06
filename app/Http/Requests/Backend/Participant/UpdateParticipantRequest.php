<?php namespace App\Http\Requests\Backend\Participant;

use App\Http\Requests\Request;

class UpdateParticipantRequest extends Request {

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
			'email'	=>  'required|email',
			'name'	=>  'required',
                        'nrc_id' => 'required',
                        'avatar' => 'string',
		];
	}
}