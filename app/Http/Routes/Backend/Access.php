<?php

Route::group(['prefix' => 'access', 'namespace' => 'Access'], function ()
{
	/* User Management */
        Route::get('users', ['as' => 'admin.access.users.index', 'uses' => 'UserController@index']);
        Route::post('users', ['as' => 'admin.access.users.store', 'uses' => 'UserController@store']);
        Route::match(['get','head'],'users/create', ['as' => 'admin.access.users.create', 'uses' => 'UserController@create']);
        Route::match(['patch','put'],'users/{users}', ['as' => 'admin.access.users.update', 'uses' => 'UserController@update']);
        Route::delete('users/{users}', ['as' => 'admin.access.users.destroy', 'uses' => 'UserController@destroy']);
        Route::get('users/{users}/edit', ['as' => 'admin.access.users.edit', 'uses' => 'UserController@edit']);
	//Route::resource('users', 'UserController', ['except' => ['show']]);

	Route::get('users/deactivated', ['as' => 'admin.access.users.deactivated', 'uses' => 'UserController@deactivated']);
	Route::get('users/banned', ['as' => 'admin.access.users.banned', 'uses' => 'UserController@banned']);
	Route::get('users/deleted', ['as' => 'admin.access.users.deleted', 'uses' => 'UserController@deleted']);
	Route::get('account/confirm/resend/{user_id}', ['as' => 'admin.account.confirm.resend', 'uses' => 'UserController@resendConfirmationEmail']);
	Route::get('users/search', ['as' => 'admin.access.users.search', 'uses' => 'UserController@search']);

	
        /* Specific User */
	Route::group(['prefix' => 'user/{id}', 'where' => ['id' => '[0-9]+']], function () {
		Route::get('delete', ['as' => 'admin.access.user.delete-permanently', 'uses' => 'UserController@delete']);
		Route::get('restore', ['as' => 'admin.access.user.restore', 'uses' => 'UserController@restore']);
		Route::get('mark/{status}', ['as' => 'admin.access.user.mark', 'uses' => 'UserController@mark'])
			->where([
				'status' => '[0,1,2]'
			]);
		Route::get('password/change', ['as' => 'admin.access.user.change-password', 'uses' => 'UserController@changePassword']);
		Route::post('password/change', ['as' => 'admin.access.user.change-password', 'uses' => 'UserController@updatePassword']);
	});

	/* Roles Management */
        Route::get('roles', ['as' => 'admin.access.roles.index', 'uses' => 'RoleController@index']);
        Route::post('roles', ['as' => 'admin.access.roles.store', 'uses' => 'RoleController@store']);
        Route::match(['get','head'],'roles/create', ['as' => 'admin.access.roles.create', 'uses' => 'RoleController@create']);
        Route::match(['patch','put'],'roles/{roles}', ['as' => 'admin.access.roles.update', 'uses' => 'RoleController@update']);
        Route::delete('roles/{roles}', ['as' => 'admin.access.roles.destroy', 'uses' => 'RoleController@destroy']);
        Route::get('roles/{roles}/edit', ['as' => 'admin.access.roles.edit', 'uses' => 'RoleController@edit']);
	//Route::resource('roles', 'RoleController', ['except' => ['show']]);
        
        /* Organization Management */
        Route::get('organizations', ['as' => 'admin.access.organizations.index', 'uses' => 'OrganizationController@index']);
        Route::post('organizations', ['as' => 'admin.access.organizations.store', 'uses' => 'OrganizationController@store']);
        Route::match(['get','head'],'organizations/create', ['as' => 'admin.access.organizations.create', 'uses' => 'OrganizationController@create']);
        Route::match(['patch','put'],'organizations/{organizations}', ['as' => 'admin.access.organizations.update', 'uses' => 'OrganizationController@update']);
        Route::delete('organizations/{organizations}', ['as' => 'admin.access.organizations.destroy', 'uses' => 'OrganizationController@destroy']);
        Route::get('organizations/{organizations}/edit', ['as' => 'admin.access.organizations.edit', 'uses' => 'OrganizationController@edit']);
	//Route::resource('organizations', 'OrganizationController', ['except' => ['show']]);

	/* Permission Management */
	Route::group(['prefix' => 'roles'], function ()
	{
            Route::get('permissions', ['as' => 'admin.access.roles.permissions.index', 'uses' => 'PermissionController@index']);
            Route::post('permissions', ['as' => 'admin.access.roles.permissions.store', 'uses' => 'PermissionController@store']);
            Route::match(['get','head'],'permissions/create', ['as' => 'admin.access.roles.permissions.create', 'uses' => 'PermissionController@create']);
            Route::match(['patch','put'],'permissions/{permissions}', ['as' => 'admin.access.roles.permissions.update', 'uses' => 'PermissionController@update']);
            Route::delete('permissions/{permissions}', ['as' => 'admin.access.roles.permissions.destroy', 'uses' => 'PermissionController@destroy']);
            Route::get('permissions/{permissions}/edit', ['as' => 'admin.access.roles.permissions.edit', 'uses' => 'PermissionController@edit']);	
            //Route::resource('permissions', 'PermissionController', ['except' => ['show']]);
	});
        
        
});