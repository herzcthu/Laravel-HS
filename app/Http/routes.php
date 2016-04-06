<?php
Route::group(['prefix' => 'lang', 'middleware' => ['locale']], function(){
  Route::get('{lang}', function($lang){
    Translation::setLocale($lang);
    $previous = URL::previous();
    $new = preg_replace('/(\.[a-z]{2,5})(\/[a-z]{0,2})?\//', "$1/".$lang."/", $previous);
    return Redirect::to($new);
});      
});

Route::group(['prefix' => Translation::getRoutePrefix(), 'middleware' => ['locale']], function()
{
    Route::group(['middleware' => 'auth'], function ()
	{
        Route::group([
			'middleware' => 'access.routeNeedsRoleOrPermission',
			'role'       => ['Administrator'],
			'permission' => ['view_backend'],
			'redirect'   => '/',
			'with'       => ['flash_danger', 'You do not have access to do that.']
		], function ()
		{
        Route::get('download/{file}', function($file){ 
            $file_path = storage_path() .'/'. $file;//dd($file_path);
            if (file_exists($file_path))
            {
                // Send Download
                $filename = preg_filter('/[^\d\w\s \.]/', '_', $file);
                return Response::download($file_path, $filename, [
                    'Content-Length: '. filesize($file_path)
                ], 'attachment');
            }
            else
            {
                // Error
                exit('Requested file does not exist on our server!');
            }
        })->where(['file' => '(.*)']);
                });
        });
/**
 * Frontend Routes
 * Namespaces indicate folder structure
 */
Route::group(['namespace' => 'Frontend'], function ()
{
	require(__DIR__ . "/Routes/Frontend/Frontend.php");
	require(__DIR__ . "/Routes/Frontend/Access.php");
});

/**
 * Backend Routes
 * Namespaces indicate folder structure
 */
Route::group(['namespace' => 'Backend'], function ()
{
	Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function ()
	{
		/**
		 * These routes need the Administrator Role
		 * or the view-backend permission (good if you want to allow more than one group in the backend, then limit the backend features by different roles or permissions)
		 *
		 * If you wanted to do this in the controller it would be:
		 * $this->middleware('access.routeNeedsRoleOrPermission:{role:Administrator,permission:view_backend,redirect:/,with:flash_danger|You do not have access to do that.}');
		 *
		 * You could also do the above in the Route::group below and remove the other parameters, but I think this is easier to read here.
		 * Note: If you have both, the controller will take precedence.
		 */
		Route::group([
			'middleware' => 'access.routeNeedsRoleOrPermission',
			'role'       => ['Administrator'],
			'permission' => ['view_backend'],
			'redirect'   => '/',
			'with'       => ['flash_danger', 'You do not have access to do that.']
		], function ()
		{
			Route::get('dashboard', ['as' => 'backend.dashboard', 'uses' => 'DashboardController@index']);
                        Route::get('media', ['as' => 'backend.media', 'uses' => 'MediaController@index']);
			require(__DIR__ . "/Routes/Backend/Access.php");
		});
                /* Result Management */
                Route::group([
                        'middleware' => 'access.routeNeedsPermission',
                        'permission' => ['add_result'], 
                        'redirect'   => '/',
                        'with'       => ['flash_danger', 'You do not have access to do that.']
                ], function ()
                {                        

                    Route::get('language', ['as' => 'admin.language.index', 'uses' => 'LocalizationController@index']);
                    Route::match(['patch','put'],'language', ['as' => 'backend.language.update', 'uses' => 'LocalizationController@update']);

                });
                require(__DIR__ . "/Routes/Backend/Participant.php");
                require(__DIR__ . "/Routes/Backend/Project.php");
                require(__DIR__ . "/Routes/Backend/General.php");
	});
});

/**
 * Backend Routes
 * Namespaces indicate folder structure
 */
//Route::group(['namespace' => 'Ajax', 'middleware' => 'ajax'], function ()
Route::group(['namespace' => 'Ajax'], function ()
{
    require(__DIR__ . "/Routes/Ajax/Ajax.php");
});
});