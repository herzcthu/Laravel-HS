<?php

/*
 * Global helpers file with misc functions
 */

if (! function_exists('app_name')) {
	/**
	 * Helper to grab the application name
	 *
	 * @return mixed
	 */
	function app_name() {
		return config('app.name');
	}
}

if ( ! function_exists('access'))
{
	/**
	 * Access (lol) the Access:: facade as a simple function
	 */
	function access()
	{
		return app('access');
	}
}

if ( ! function_exists('javascript'))
{
	/**
	 * Access the javascript helper
	 */
	function javascript()
	{
		return app('JavaScript');
	}
}


if ( ! function_exists('aio'))
{
	/**
	 * Aio (lol) the Aio:: facade as a simple function
	 */
	function aio()
	{
		return app('aio');
	}
}

/**
 * array_filter recursively to remove empty values in an array
 */
if ( !function_exists('array_filter_recursive'))
{
        function array_filter_recursive($input) 
          { 
            foreach ($input as &$value) 
            { 
              if (is_array($value)) 
              { 
                $value = array_filter_recursive($value); 
              } 
            } 

            return array_filter($input); 
          } 
}