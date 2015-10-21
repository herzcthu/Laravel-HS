<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RedirectIfNotAjax
{
/**
	 * The Guard implementation.
	 *
	 * @var Guard
	 */
	protected $auth;

	/**
	 * Create a new filter instance.
	 *
	 * @param  Guard  $auth
	 * @return void
	 */
	public function __construct(Guard $auth)
	{
		$this->auth = $auth;
	}    
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->ajax())
	{
            if ($this->auth->check())
		{
			//$ajax = new RedirectResponse(url('/dashboard'));
                        return redirect()->route('home')->withFlashDanger('Redirected: You cannot request that page directly!');
		}
            if ($this->auth->guest())
                {
                        return redirect()->guest('auth/login')->withFlashDanger('Redirected: You cannot request that page directly!');
                }                
	}
        return $next($request);
    }
}
