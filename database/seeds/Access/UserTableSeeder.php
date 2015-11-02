<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon as Carbon;

class UserTableSeeder extends Seeder {

	public function run() {

		DB::statement('SET FOREIGN_KEY_CHECKS=0;');

		//Add the master administrator, user id of 1
		DB::table(config('auth.table'))->truncate();

		$users = [
			[       //user ID 1
				'name' => 'Admin Istrator',
                                'avatar' => '/img/frontend/user.png',
				'email' => 'admin@admin.com',
				'password' => bcrypt('1234'),
				'confirmation_code' => md5(uniqid(mt_rand(), true)),
				'confirmed' => true,
				'created_at' => Carbon::now(),
				'updated_at' => Carbon::now()
			],/**
			[       //user ID 2
				'name' => 'Default User',
                                'avatar' => '/img/frontend/user.png',
				'email' => 'user@user.com',
				'password' => bcrypt('1234'),
				'confirmation_code' => md5(uniqid(mt_rand(), true)),
				'confirmed' => true,
				'created_at' => Carbon::now(),
				'updated_at' => Carbon::now()
			],
                        [       //user ID 3
				'name' => 'Org Manager',
                                'avatar' => '/img/frontend/user.png',
				'email' => 'om@om.com',
				'password' => bcrypt('1234'),
				'confirmation_code' => md5(uniqid(mt_rand(), true)),
				'confirmed' => true,
				'created_at' => Carbon::now(),
				'updated_at' => Carbon::now()
			],
                        [       //user ID 4
				'name' => 'Projects Manager',
                                'avatar' => '/img/frontend/user.png',
				'email' => 'pm@pm.com',
				'password' => bcrypt('1234'),
				'confirmation_code' => md5(uniqid(mt_rand(), true)),
				'confirmed' => true,
				'created_at' => Carbon::now(),
				'updated_at' => Carbon::now()
			],
                        [       //user ID 5
				'name' => 'Data Clerk',
                                'avatar' => '/img/frontend/user.png',
				'email' => 'dc@dc.com',
				'password' => bcrypt('1234'),
				'confirmation_code' => md5(uniqid(mt_rand(), true)),
				'confirmed' => true,
				'created_at' => Carbon::now(),
				'updated_at' => Carbon::now()
			],
                         * 
                         */
		];

		DB::table(config('auth.table'))->insert($users);

		DB::statement('SET FOREIGN_KEY_CHECKS=1;');
	}
}