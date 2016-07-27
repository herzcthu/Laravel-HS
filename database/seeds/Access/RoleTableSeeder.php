<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon as Carbon;

class RoleTableSeeder extends Seeder {

	public function run() {

		DB::statement('SET FOREIGN_KEY_CHECKS=0;');

		DB::table(config('access.roles_table'))->truncate();

		//Create admin role, id of 1
		$role_model = config('access.role');
		$admin = new $role_model;
		$admin->name = 'Administrator';
                $admin->level = '0';
		$admin->created_at = Carbon::now();
		$admin->updated_at = Carbon::now();
		$admin->save();

		//id = 2
		$user = new $role_model;
		$user->name = 'User';
                $user->level = '99';
		$user->created_at = Carbon::now();
		$user->updated_at = Carbon::now();
		$user->save();
                
                //id = 3
		$orgmanager = new $role_model;
		$orgmanager->name = 'Organizations Manager';
                $orgmanager->level = '1';
		$orgmanager->created_at = Carbon::now();
		$orgmanager->updated_at = Carbon::now();
		$orgmanager->save();
                
                //id = 4
		$projectmanager = new $role_model;
		$projectmanager->name = 'Projects Manager';
                $projectmanager->level = '2';
		$projectmanager->created_at = Carbon::now();
		$projectmanager->updated_at = Carbon::now();
		$projectmanager->save();
                
                //id = 5
		$dataclerk = new $role_model;
		$dataclerk->name = 'Data Clerk';
                $dataclerk->level = '10';
		$dataclerk->created_at = Carbon::now();
		$dataclerk->updated_at = Carbon::now();
		$dataclerk->save();

		DB::statement('SET FOREIGN_KEY_CHECKS=1;');
	}
}