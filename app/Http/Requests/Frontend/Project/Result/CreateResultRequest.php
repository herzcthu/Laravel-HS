<?php namespace App\Http\Requests\Frontend\Project\Result;

use App\Http\Requests\Request;

class CreateResultRequest extends Request {

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
        // Here we can do more with the validation instance...
    /**
     * This method implement validator method in Request class
     * @param type $validator
     */
    public function dataentryValidation($validator) {
        
        // Use an "after validation hook" (see laravel docs)
        /**
        $validator->after(function ($validator) {
            $section = (integer) $this->route('section');
            $project = $this->route('project');
            $section_qcount = $project->questions->where('section', $section)->count();
            $answers = count($this->input('answer'));
           
          // if ($answers !== $section_qcount) {
               
	//	$validator->errors()->add('answer', 'You need to fill all questions in one section.');

         //   }  
        });
         * *
         */
    }
        
}