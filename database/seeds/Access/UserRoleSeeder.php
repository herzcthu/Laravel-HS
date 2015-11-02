<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRoleSeeder extends Seeder {

	public function run() {

		DB::statement('SET FOREIGN_KEY_CHECKS=0;');

		DB::table(config('access.assigned_roles_table'))->truncate();

                $role_model = config('access.role');
                $role_model = new $role_model;
		$role1 = $role_model::first();
		//Attach admin role to admin user
		$user_model = config('auth.model');
		$user_model = new $user_model;//dd($role1);
		$user1 = $user_model::first();
                $user1->role()->associate($role1);
                $user1->save();
                /**
		//Attach user role to general user
		$user_model = config('auth.model');
		$user_model = new $user_model;
                $role2 = $role_model::find(2);
		$user_model::find(2)->role()->associate($role2)->save();
                
                //Attach coordinator role to coordinator
		$user_model = config('auth.model');
		$user_model = new $user_model;
                $role3 = $role_model::find(3);
		$user_model::find(3)->role()->associate($role3)->save();
                
                //Attach spot checker role to spot checker
		$user_model = config('auth.model');
		$user_model = new $user_model;
                $role4 = $role_model::find(4);
		$user_model::find(4)->role()->associate($role4)->save();
                
                //Attach enumerator role to enumerator
		$user_model = config('auth.model');
		$user_model = new $user_model;
                $role5 = $role_model::find(5);
		$user_model::find(5)->role()->associate($role5)->save();
                 * 
                 */


		DB::statement('SET FOREIGN_KEY_CHECKS=1;');
	}
}