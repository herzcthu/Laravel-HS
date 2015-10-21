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


Route::group(['namespace' => 'Participant'], function ()
{
                Route::group(['prefix' => 'participants'], function ()
                {
                /* Roles Management */
                Route::get('proles', ['as' => 'admin.participants.proles.index', 'uses' => 'ProleController@index']);
                Route::post('proles', ['as' => 'admin.participants.proles.store', 'uses' => 'ProleController@store']);
                Route::match(['get','head'],'proles/create', ['as' => 'admin.participants.proles.create', 'uses' => 'ProleController@create']);
                Route::match(['patch','put'],'proles/{proles}', ['as' => 'admin.participants.proles.update', 'uses' => 'ProleController@update']);
                Route::delete('proles/{proles}', ['as' => 'admin.participants.proles.destroy', 'uses' => 'ProleController@destroy']);
                Route::get('proles/{proles}/edit', ['as' => 'admin.participants.proles.edit', 'uses' => 'ProleController@edit']);    
                //Route::resource('proles', 'RoleController', ['except' => ['show']]);
                });
                /* Participant Management */
                Route::group([
			'middleware' => 'access.routeNeedsPermission',
			'permission' => ['access_participant'], 
			'redirect'   => '/',
			'with'       => ['flash_danger', 'You do not have access to do that.']
		], function ()
		{
                    Route::post('participants/bulk', ['as' => 'admin.participants.bulk', 'uses' => 'ParticipantController@bulk']);
                    Route::get('participants/search', ['as' => 'admin.participants.search', 'uses' => 'ParticipantController@search']);
                    Route::get('participants', ['as' => 'admin.participants.index', 'uses' => 'ParticipantController@index']);
                });             
                
                /* Participant Management */
                Route::group([
			'middleware' => 'access.routeNeedsPermission',
			'permission' => ['manage_participant'],
			'redirect'   => '/',
			'with'       => ['flash_danger', 'You do not have access to do that.']
		], function ()
		{
                    Route::get('participants/import', ['as' => 'admin.participants.import', 'uses' => 'ParticipantController@showImport']);
                    Route::post('participants/import', ['as' => 'admin.participants.import', 'uses' => 'ParticipantController@import']);
                    Route::get('participants', ['as' => 'admin.participants.index', 'uses' => 'ParticipantController@index']);
                    Route::post('participants', ['as' => 'admin.participants.store', 'uses' => 'ParticipantController@store']);
                    Route::match(['get','head'],'participants/create', ['as' => 'admin.participants.create', 'uses' => 'ParticipantController@create']);
                    Route::match(['patch','put'],'participants/{participants}', ['as' => 'admin.participants.update', 'uses' => 'ParticipantController@update']);
                    Route::delete('participants/{participants}', ['as' => 'admin.participants.destroy', 'uses' => 'ParticipantController@destroy']);
                    Route::get('participants/{participants}/edit', ['as' => 'admin.participants.edit', 'uses' => 'ParticipantController@edit']);
                    //Route::resource('participants', 'ParticipantController', ['except' => ['show']]);                    
                    Route::get('participants/deleted', ['as' => 'admin.participants.deleted', 'uses' => 'ParticipantController@deleted']);
                }); 
                
                /* Specific User */
                Route::group(['prefix' => 'participant/{id}', 'where' => ['id' => '[0-9]+']], function () {
                        Route::get('delete', ['as' => 'admin.participant.delete-permanently', 'uses' => 'ParticipantController@delete']);
                        Route::get('restore', ['as' => 'admin.participant.restore', 'uses' => 'ParticipantController@restore']);                        
                });
    
});