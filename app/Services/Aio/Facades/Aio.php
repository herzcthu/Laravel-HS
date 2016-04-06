<?php namespace App\Services\Aio\Facades;

/**
 * Class Access
 * @package App\Services\Access\Facades
 */
class Aio extends \Illuminate\Support\Facades\Facade
{
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{
		return 'aio';
	}
}