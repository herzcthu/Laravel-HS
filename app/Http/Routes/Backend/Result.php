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

                /* Result Management */
                Route::group([
			'middleware' => 'access.routeNeedsPermission',
			'permission' => ['manage_result'],
			'redirect'   => '/',
			'with'       => ['flash_danger', 'You do not have access to do that.']
		], function ()
		{                    
                    Route::resource('project.results', 'ResultController', ['except' => ['show','edit']]); 
                }); 
                
                Route::group(['prefix' => 'project'], function(){
                    Route::group([
                        'prefix' => '{project}/results'
                    ], function(){
                        Route::post('section/{section}/store', ['as' => 'admin.project.results.section.store', 'uses' => 'ResultController@store']);
                        Route::patch('section/{section}/update', ['as' => 'admin.project.results.section.update', 'uses' => 'ResultController@update']);
                        Route::post('bulk', ['as' => 'admin.project.results.bulk', 'uses' => 'ResultController@bulk']);
                        Route::get('edit', ['as' => 'admin.project.results.editall', 'uses' => 'ResultController@editall']);
                    });
                    Route::group([
                        'prefix' => 'result/{id}', 
                        'where' => ['id' => '[0-9]+'],
                        'middleware' => 'access.routeNeedsPermission',
                        'permission' => ['manage_result'],
                        'redirect'   => '/',
                        'with'       => ['flash_danger', 'You do not have access to do that.']
                        ], function () {
                            Route::get('edit', ['as' => 'admin.project.result.edit', 'uses' => 'ResultController@edit']); 
                            Route::get('delete', ['as' => 'admin.project.result.delete-permanently', 'uses' => 'ResultController@delete']);
                            Route::get('restore', ['as' => 'admin.project.result.restore', 'uses' => 'ResultController@restore']); 
                    });
                    
                    /* Result Management */
                    Route::group([
                            'middleware' => 'access.routeNeedsPermission',
                            'permission' => ['access_result'], 
                            'redirect'   => '/',
                            'with'       => ['flash_danger', 'You do not have access to do that.']
                    ], function ()
                    {                        
                        Route::get('{project}/results/search', ['as' => 'admin.project.results.search', 'uses' => 'ResultController@search']);
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
                    
                });