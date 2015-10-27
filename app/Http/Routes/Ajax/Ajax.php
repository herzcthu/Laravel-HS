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

Route::group(['middleware' => 'auth'], function ()
{
    Route::group(['prefix' => 'ajax'], function ()
    {
        Route::get('project/{project}/statuscount', ['as' => 'ajax.project.statuscount', 'uses' => 'AjaxController@getStatusCount']);
        Route::get('project/{project}/timegraph', ['as' => 'ajax.project.timegraph', 'uses' => 'AjaxController@timeGraph']);
        Route::get('project/{project}/response', ['as' => 'ajax.project.response', 'uses' => 'AjaxController@getResponse']);
        Route::get('project/{project}/status', ['as' => 'ajax.project.status', 'uses' => 'AjaxController@getAllStatus']);
        Route::get('project/{project}/results', ['as' => 'ajax.project.results', 'uses' => 'AjaxController@getAllResults']);
        Route::get('locations/allstates', ['as' => 'ajax.locations.allstates', 'uses' => 'AjaxController@allstates']);
        Route::get('locations/alldistricts', ['as' => 'ajax.locations.alldistricts', 'uses' => 'AjaxController@alldistricts']);
        Route::get('locations/alltownships', ['as' => 'ajax.locations.alltownships', 'uses' => 'AjaxController@alltownships']);
        Route::get('locations/allvillagetracks', ['as' => 'ajax.locations.allvillagetracks', 'uses' => 'AjaxController@allvillagetracks']);
        Route::get('locations/allvillages', ['as' => 'ajax.locations.allvillages', 'uses' => 'AjaxController@allvillages']);
        Route::get('locations/searchname', ['as' => 'ajax.locations.searchname', 'uses' => 'AjaxController@searchLocationsOnlyName']);
        Route::match(['post','put', 'patch'],'language',['as' => 'ajax.language', 'uses' => 'AjaxController@updateTranslation']);
        
    });
    Route::group(['prefix' => 'ajax/locations/{id}', 'where' => ['id' => '[0-9]+']], function () {
            Route::get('villages', ['as' => 'ajax.locations.villages_by_id', 'uses' => 'AjaxController@villages_by_id']);
            Route::get('villagetracks', ['as' => 'ajax.locations.villagetracks_by_id', 'uses' => 'AjaxController@villagetracks_by_id']);
            Route::get('townships', ['as' => 'ajax.locations.townships_by_id', 'uses' => 'AjaxController@townships_by_id']);
            Route::get('districts', ['as' => 'ajax.locations.districts_by_id', 'uses' => 'AjaxController@districts_by_id']);
            Route::get('states', ['as' => 'ajax.locations.states_by_id', 'uses' => 'AjaxController@states_by_id']);
        });
    Route::group(['prefix' => 'ajax/project/{project}'], function () {
        Route::get('/pcode/{pcode}', ['as' => 'ajax.project.pcode', 'uses' => 'AjaxController@formValidatePcode']);
        Route::get('/person/{person}', ['as' => 'ajax.project.person', 'uses' => 'AjaxController@formValidatePerson']);
    });    
});