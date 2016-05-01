<?php

/**
 * Frontend Controllers
 */
Route::get('/', ['as' => 'home', 'uses' => 'FrontendController@index']);
Route::get('macros', 'FrontendController@macros');

/**
 * These frontend controllers require the user to be logged in
 */
Route::group(['middleware' => 'auth'], function ()
{
	Route::get('dashboard', ['as' => 'frontend.dashboard', 'uses' => 'DashboardController@index']);
        Route::get('locations', ['as' => 'frontend.locations', 'uses' => 'LocationController@index']);
        //Route::resource('media', 'MediaController', ['only' => ['show', 'edit', 'update']]);
        Route::group(['prefix' => 'data'], function(){
            Route::get('projects', ['as' => 'data.projects.index', 'uses' => 'ProjectController@index']);
            
            Route::get('project/{project}/status', ['as' => 'data.project.status.index', 'uses' => 'StatusController@index']);
            
            Route::get('project/{project}/response', ['as' => 'data.project.response.index', 'uses' => 'StatusController@response']);
            
            Route::get('project/{project}/iresponse', ['as' => 'data.project.iresponse.index', 'uses' => 'StatusController@iresponse']);
            
            
            /* Result Management */
            Route::group([
                    'middleware' => 'access.routeNeedsPermission',
                    'prefix' => 'project/{project}',
                    'permission' => ['add_result'], 
                    'redirect'   => '/',
                    'with'       => ['flash_danger', 'You do not have access to do that.']
            ], function ()
            {
            Route::get('/results', ['as' => 'data.project.results.index', 'uses' => 'ResultController@index']);
            Route::get('/results/create', ['as' => 'data.project.results.create', 'uses' => 'ResultController@create']);
            Route::get('/results/{result}/edit', ['as' => 'data.project.results.edit', 'uses' => 'ResultController@edit']);
            Route::post('/results/store', ['as' => 'data.project.results.store', 'uses' => 'ResultController@store']);
            Route::patch('/results/update', ['as' => 'data.project.results.update', 'uses' => 'ResultController@update']);
            //Route::resource('project.results', 'ResultController', ['except' => ['show','edit']]); 
            });
        });
        Route::get('profile', ['as' => 'profile.index', 'uses' => 'ProfileController@index']);
        Route::get('profile/{profile}/edit', ['as' => 'profile.edit', 'uses' => 'ProfileController@edit']);
        Route::patch('profile/{profile}/update', ['as' => 'profile.update', 'uses' => 'ProfileController@update']);
        //Route::resource('profile', 'ProfileController', ['only' => ['index','edit', 'update']]);
});