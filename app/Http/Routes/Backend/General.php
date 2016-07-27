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

                /* Update many users medias at once */
                Route::group([
			'middleware' => 'access.routeNeedsMediaOrPermission',
			'media'       => [''],
			'permission' => ['edit_users'],
			'redirect'   => '/',
			'with'       => ['flash_danger', 'You do not have access to do that.']
		], function ()
		{
                    Route::post('users/bulk', ['as' => 'admin.access.users.bulk', 'uses' => 'Access\UserController@bulk']);
                });
                /* Upload files */
                Route::group([
			'middleware' => 'access.routeNeedsPermission',
			'permission' => ['manage_media'],
			'redirect'   => '/',
			'with'       => ['flash_danger', 'You do not have access to do that.']
		], function ()
		{
                    Route::get('media', ['as' => 'admin.media.index', 'uses' => 'MediaController@index']);
                    //Route::post('media', ['as' => 'admin.media.store', 'uses' => 'MediaController@store']);
                    //Route::match(['get','head'],'media/create', ['as' => 'admin.media.create', 'uses' => 'MediaController@create']);
                    Route::match(['patch','put'],'media/{media}', ['as' => 'admin.media.update', 'uses' => 'MediaController@update']);
                    Route::delete('media/{media}', ['as' => 'admin.media.destroy', 'uses' => 'MediaController@destroy']);
                    Route::get('media/{media}/edit', ['as' => 'admin.media.edit', 'uses' => 'MediaController@edit']);
                    //Route::resource('media', 'MediaController', ['except' => ['show','create','store']]);
                });
                /* Upload files */
                Route::post('media/upload',[
			'middleware' => 'access.routeNeedsPermission',
			'permission' => ['upload_media'],
			'redirect'   => '/',
			'with'       => ['flash_danger', 'You do not have access to do that.'],
                        'as' => 'admin.media.upload', 
                        'uses' => 'MediaController@upload'
		]);
                
                /* Location Management */
                Route::group([
			'middleware' => 'access.routeNeedsPermission',
			'permission' => ['access_location'], 
			'redirect'   => '/',
			'with'       => ['flash_danger', 'You do not have access to do that.']
		], function ()
		{
                    Route::get('locations/search', ['as' => 'admin.locations.search', 'uses' => 'LocationController@search']);
                    Route::get('locations/deleteall', ['as' => 'admin.locations.deleteall', 'uses' => 'LocationController@deleteAll']);
                    Route::get('locations', ['as' => 'admin.locations.index', 'uses' => 'LocationController@index']);
                });
                
                /* Location Management */
                Route::group([
			'middleware' => 'access.routeNeedsPermission',
			'permission' => ['manage_location'],
			'redirect'   => '/',
			'with'       => ['flash_danger', 'You do not have access to do that.']
		], function ()
		{
                    Route::get('locations/import', ['as' => 'admin.locations.import', 'uses' => 'LocationController@showImport']);
                    Route::post('locations/import', ['as' => 'admin.locations.import', 'uses' => 'LocationController@import']);
                    Route::get('locations', ['as' => 'admin.locations.index', 'uses' => 'LocationController@index']);
                    Route::post('locations', ['as' => 'admin.locations.store', 'uses' => 'LocationController@store']);
                    Route::match(['get','head'],'locations/create', ['as' => 'admin.locations.create', 'uses' => 'LocationController@create']);
                    Route::match(['patch','put'],'locations/{locations}', ['as' => 'admin.locations.update', 'uses' => 'LocationController@update']);
                    Route::delete('locations/{locations}', ['as' => 'admin.locations.destroy', 'uses' => 'LocationController@destroy']);
                    Route::get('locations/{locations}/edit', ['as' => 'admin.locations.edit', 'uses' => 'LocationController@edit']);
                    Route::resource('locations', 'LocationController', ['except' => ['index']]);
                });