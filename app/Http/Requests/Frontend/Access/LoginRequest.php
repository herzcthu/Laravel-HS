<?php namespace App\Http\Requests\Frontend\Access;

use App\Http\Requests\Request;

class LoginRequest extends Request {

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
			'email' 	=> 'required|email',
			'password'  => 'required',
		];
	}
        public function response(array $errors)
        {

            if ($this->ajax() || $this->wantsJson()) {
                return new JsonResponse($errors, 422);
            }
            if($errors){
            return $this->redirector->to('auth/login')
                                            ->withInput($this->except($this->dontFlash))
                                            ->withErrors($errors, $this->errorBag);
            }else{
                return $this->redirector->to($this->getRedirectUrl())
                                            ->withInput($this->except($this->dontFlash))
                                            ->withErrors($errors, $this->errorBag);
            }
        }        
}