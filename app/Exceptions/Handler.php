<?php namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler {

	/**
	 * A list of the exception types that should not be reported.
	 *
	 * @var array
	 */
	protected $dontReport = [
		HttpException::class,
	];

	/**
	 * Report or log an exception.
	 *
	 * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
	 *
	 * @param  \Exception  $e
	 * @return void
	 */
	public function report(Exception $e)
	{
		return parent::report($e);
	}

	/**
	 * Render an exception into an HTTP response.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Exception  $e
	 * @return \Illuminate\Http\Response
	 */
	public function render($request, Exception $e)
	{
		//As to preserve the catch all
		if ($e instanceof GeneralException)
		{
			return redirect()->back()->withInput()->withFlashDanger($e->getMessage());
		}

		if ($e instanceof Backend\Access\User\UserNeedsRolesException)
		{
			return redirect()->route('admin.access.users.edit', $e->userID())->withInput()->withFlashDanger($e->validationErrors());
		}
                
                if ($e instanceof \Illuminate\Session\TokenMismatchException)
                {
                    return redirect()
                            ->back()
                            ->withInput($request->except('password'))
                            ->withFlashDanger($e->getMessage());
                }   

		//Catch all
		return parent::render($request, $e);
	}
}