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

$factory->define(Illuminate\Notifications\DatabaseNotification::class, function(Faker $faker) {
	return [
		'id' => \Ramsey\Uuid\Uuid::uuid4()->toString(),
		'type' =>  'App\Notifications\ThreadWasUpdated',
		'notifiable_id' => function() {
			return auth()->id() ?: factory('App\User')->create()->id;
		},
		'notifiable_type' => 'App\User',
		'data' => [$faker->word => $faker->sentence],
	];
});