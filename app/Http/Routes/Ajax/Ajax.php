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
        Route::get('locations/search', ['as' => 'ajax.locations.searchname', 'uses' => 'AjaxController@searchLocationsOnlyName']);
        Route::match(['post','put', 'patch'],'language',['as' => 'ajax.language', 'uses' => 'AjaxController@updateTranslation']);
        Route::delete('participants/{participant}/location/{location}', ['as' => 'ajax.participants.delocate', 'uses' => 'AjaxController@delocate']);
        
    });
    Route::group(['prefix' => 'ajax/project/{project}'], function () {
        Route::get('/', ['as' => 'ajax.project.base', 'uses' => 'AjaxController@getProject']);
        
        Route::get('/statuscount', ['as' => 'ajax.project.statuscount', 'uses' => 'AjaxController@getStatusCount']);
        Route::get('/timegraph', ['as' => 'ajax.project.timegraph', 'uses' => 'AjaxController@timeGraph']);
        Route::get('/response', ['as' => 'ajax.project.response', 'uses' => 'AjaxController@getResponse']);
        Route::get('/status', ['as' => 'ajax.project.status', 'uses' => 'AjaxController@getAllStatus']);
        Route::get('/results', ['as' => 'ajax.project.results', 'uses' => 'AjaxController@getAllResults']);
        Route::get('/survey', ['as' => 'ajax.project.survey', 'uses' => 'AjaxController@getSurveyLists']);
        Route::post('/questions/sort', ['as' => 'ajax.project.questions.sort', 'uses' => 'AjaxController@sortQuestions']);
        Route::post('/question/new', ['as' => 'ajax.project.question.new', 'uses' => 'AjaxController@newQuestion']);
        Route::get('/question/{questions}/edit', ['as' => 'ajax.project.question.edit', 'uses' => 'AjaxController@getQuestion']);
        Route::match(['patch','put'],'/question/{questions}/edit', ['as' => 'ajax.project.question.edit', 'uses' => 'AjaxController@editQuestion']);
        Route::match(['patch','put'],'/question/{questions}/addlogic', ['as' => 'ajax.project.question.addlogic', 'uses' => 'AjaxController@addLogic']); 

        Route::get('/pcode/{pcode}', ['as' => 'ajax.project.pcode', 'uses' => 'AjaxController@formValidatePcode']);
        Route::get('/person/{person}', ['as' => 'ajax.project.person', 'uses' => 'AjaxController@formValidatePerson']);
        
        Route::get('/questions',['as' => 'ajax.project.questions', 'uses' => 'AjaxController@getQuestions']);            
        Route::get('/question/{questions}/answers',['as' => 'ajax.project.question.answers', 'uses' => 'AjaxController@getAnswers']);
    });    
});