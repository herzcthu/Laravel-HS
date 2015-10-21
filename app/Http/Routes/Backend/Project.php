<?php

/* 
 * Copyright (C) 2015 sithu
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */


                
                /* Project Management */
                Route::group([
			'middleware' => 'access.routeNeedsPermission',
			'permission' => ['access_project'], 
			'redirect'   => '/',
			'with'       => ['flash_danger', 'You do not have access to do that.']
		], function ()
		{
                    Route::post('projects/bulk', ['as' => 'admin.projects.bulk', 'uses' => 'ProjectController@bulk']);
                    Route::get('projects/search', ['as' => 'admin.projects.search', 'uses' => 'ProjectController@search']);
                    Route::get('projects', ['as' => 'admin.projects.index', 'uses' => 'ProjectController@index']);
                });             
                
                /* Project Management */
                Route::group([
			'middleware' => 'access.routeNeedsPermission',
			'permission' => ['manage_project'],
			'redirect'   => '/',
			'with'       => ['flash_danger', 'You do not have access to do that.']
		], function ()
		{
                    /* Specific User */
                    Route::group([
                        'prefix' => 'project/{project}',
                        ], function () {
                            Route::get('edit', ['as' => 'admin.project.edit', 'uses' => 'ProjectController@edit']);
                            Route::get('destroy', ['as' => 'admin.project.destroy', 'uses' => 'ProjectController@destroy']);
                            Route::get('delete', ['as' => 'admin.project.delete-permanently', 'uses' => 'ProjectController@delete']);
                            Route::get('restore', ['as' => 'admin.project.restore', 'uses' => 'ProjectController@restore']); 
                            Route::get('analysis', ['as' => 'admin.project.analysis', 'uses' => 'ProjectController@analysis']); 
                            Route::get('export', ['as' => 'admin.project.export', 'uses' => 'ProjectController@export']);
                            Route::get('response', ['as' => 'admin.project.response', 'uses' => 'ProjectController@response']); 
                            Route::patch('update', ['as' => 'admin.project.update', 'uses' => 'ProjectController@update']);
                    });                                       
                    Route::get('projects/deleted', ['as' => 'admin.projects.deleted', 'uses' => 'ProjectController@deleted']);
                    Route::get('projects', ['as' => 'admin.projects.index', 'uses' => 'ProjectController@index']);
                    Route::post('projects', ['as' => 'admin.projects.store', 'uses' => 'ProjectController@store']);
                    Route::match(['get','head'],'projects/create', ['as' => 'admin.projects.create', 'uses' => 'ProjectController@create']);
                    Route::match(['patch','put'],'projects/{projects}', ['as' => 'admin.projects.update', 'uses' => 'ProjectController@update']);
                    Route::delete('projects/{projects}', ['as' => 'admin.projects.destroy', 'uses' => 'ProjectController@destroy']);
                    Route::get('projects/{projects}/edit', ['as' => 'admin.projects.edit', 'uses' => 'ProjectController@edit']);
                    
                    //Route::resource('projects', 'ProjectController', ['except' => ['show', 'edit']]);
                }); 
                
                
                
                //Route::group(['prefix' => 'project'], function(){
                    require(__DIR__ . "/Question.php");
                    require(__DIR__ . "/Result.php");
                //});
    
