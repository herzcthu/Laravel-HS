<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/
/**
$factory->define(App\User::class, function (Faker\Generator $faker) {
	return [
		'name' => $faker->name,
		'email' => $faker->email,
		'password' => str_random(10),
		'remember_token' => str_random(10),
	];
});
 * 
 */
/**
$factory->define(App\Location::class, function(){
    $location = [
			[
				'name' => 'Myanmar',
                                'pcode' => 'MMR',
				'type' => 'country',
				'lat' => 16.799999,
				'long' => 96.150002,
				'mya_name' => 'မြန်မာ',
				'created_at' => Carbon::now(),
				'updated_at' => Carbon::now()
			],
		];
                \App\Location::buildTree($location);
    
});
 * 
 */
$factory->define(App\Project::class, function(){
    return ['name' => $faker->name,
            'short' => $faker->regexify('[A-Z]{3,5}'),
        ]
});