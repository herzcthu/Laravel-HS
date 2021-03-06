<?php namespace App\Http\Requests\Frontend\Access;

use App\Http\Requests\Request;

class RegisterRequest extends Request {

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
			'name' 		=> 'required|max:255',
			'email' 	=> 'required|email|max:255|unique:users',
			'password'  => 'required|case_diff|numbers|letters|symbols|confirmed|min:6', //case_diff|numbers|letters|symbols
		];
	}
        
        public function response(array $errors)
        {

            if ($this->ajax() || $this->wantsJson()) {
                return new JsonResponse($errors, 422);
            }
            if($errors){
            return $this->redirector->to('auth/register')
                                            ->withInput($this->except($this->dontFlash))
                                            ->withErrors($errors, $this->errorBag);
            }else{
                return $this->redirector->to($this->getRedirectUrl())
                                            ->withInput($this->except($this->dontFlash))
                                            ->withErrors($errors, $this->errorBag);
            }
        }
}