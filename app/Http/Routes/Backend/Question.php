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

                /* Question Management */
                Route::group([
			'middleware' => 'access.routeNeedsPermission',
			'permission' => ['manage_question'],
			'redirect'   => '/',
			'with'       => ['flash_danger', 'You do not have access to do that.'],
                        'prefix'     => 'project'
		], function ()
		{                    
                    Route::get('{project}/questions', ['as' => 'admin.project.questions.index', 'uses' => 'QuestionController@index']);
                    Route::post('{project}/questions', ['as' => 'admin.project.questions.store', 'uses' => 'QuestionController@store']);
                    Route::match(['get','head'],'{project}/questions/create', ['as' => 'admin.project.questions.create', 'uses' => 'QuestionController@create']);
                    Route::match(['patch','put'],'{project}/questions/{questions}', ['as' => 'admin.project.questions.update', 'uses' => 'QuestionController@update']);
                    Route::delete('{project}/questions/{questions}', ['as' => 'admin.project.questions.destroy', 'uses' => 'QuestionController@destroy']);
                    Route::get('{project}/questions/{questions}/edit', ['as' => 'admin.project.questions.edit', 'uses' => 'QuestionController@edit']);
                    Route::get('{project}/questions/{questions}/edit', ['as' => 'admin.project.question.edit', 'uses' => 'QuestionController@edit']);
                    //Route::resource('project.questions', 'QuestionController', ['except' => ['show','edit']]); 
                }); 
                
                Route::group(['prefix' => 'project'], function(){
                    Route::group([
                        'prefix' => '{project}/questions'
                    ], function(){
                        Route::post('bulk', ['as' => 'admin.project.questions.bulk', 'uses' => 'QuestionController@bulk']);
                        Route::get('edit', ['as' => 'admin.project.questions.editall', 'uses' => 'QuestionController@editall']);
                    });
                    
                    /* Question Management */
                    Route::group([
                            'middleware' => 'access.routeNeedsPermission',
                            'permission' => ['access_question'], 
                            'redirect'   => '/',
                            'with'       => ['flash_danger', 'You do not have access to do that.']
                    ], function ()
                    {                        
                        Route::get('{project}/questions/search', ['as' => 'admin.project.questions.search', 'uses' => 'QuestionController@search']);
                    });
                    
                });
                
    
