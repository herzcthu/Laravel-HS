<?php namespace App\Http\Requests\Backend\Project;

use App\Http\Requests\Request;

class BulkUpdateProjectsRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(!$this->user()->can('edit_participants')){
            return false;
        } else {
            return true;
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
			'role' => 'required',
                        'users' => 'required',
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
           $role = $this->input('role');
           $users = $this->input('users');
           
           if ($role != "1" && $this->user()->hasRole('Administrator') && array_key_exists($this->user()->id, $users)) {
               
		$validator->errors()->add('users', 'You cannot downgrade yourself from Administrator role.');

            }
            
            
                    
        });
    }
}
