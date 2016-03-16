<?php namespace App\Http\Requests\Backend\Project\Question;

use App\Http\Requests\Request;

class UpdateQuestionRequest extends Request {

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
		$question = $this->route('questions');
                $project = $this->route('project');
                return [
			'qnum' => 'required|unique:questions,qnum,'.$question->id.',id,project_id,'.$project->id,
                        'question' => 'required',
                        'related_data' => '',
                        'project_id' => 'required',
                        'answers' => 'required',
		];
	}
        
        public function messages() {
            
            return [
              'qnum.required' => 'Question Number field is required.',
              'qnum.unique' => 'Question Number already exists in this project'  
            ];            
        }
}