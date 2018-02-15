<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Thread::class, function(Faker $faker) {
	$title = $faker->sentence;
	return [
		'user_id' => function() {
			return factory('App\User')->create()->id;
		},
		'channel_id' => function() {
			return factory('App\Channel')->create()->id;
		},
		'title' => $title,
		'body' => $faker->paragraph,
		'slug' => str_slug($title),
		'visits_count' => 0,
		'locked' => false,
	];
});