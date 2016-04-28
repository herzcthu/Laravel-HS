<?php namespace App\Http\Requests\Backend\Participant;

use App\Http\Requests\Request;

class CreateParticipantRequest extends Request {

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
			'name' =>  'required',
			'pcode_id' => ''
		];
	}
        
        // Here we can do more with the validation instance...
        /**
         * This method implement validator method in Request class
         * @param type $validator
         */
        public function dataentryValidation($validator) {

            // Use an "after validation hook" (see laravel docs)
            $validator->after(function ($validator) {
               $location = $this->input('pcode_id');
               $org_id = $this->input('org_id');
               $pcode_id = $location.'-'.$org_id;
               $pivot = \Illuminate\Support\Facades\DB::table('participant_pcode')
                       ->where('pcode_id','=',$pcode_id)
                       //->where('participant_id','!=',$participant->id)
                       ->get();
               /**
                * This rule for unique location check
                * a participant can have 2 or more location
                * but location cannot be assigned to 2 participants.
                */
               if ($pivot && config('aio.location.unique')) {

                    $validator->errors()->add('pcode_id', 'Location code already assigned to participant.');

                }
            });
        }
}