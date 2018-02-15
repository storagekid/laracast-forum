<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RealUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$pass = Hash::make('Forum%01');
        factory('App\User')->create(
        	[
        	'email' => 'jgvillalba@dentix.es',
        	'name' => 'Juan Gabriel Villalba',
        	'confirmed' => 1,
        	'password' => $pass
        	]
        );
        factory('App\User')->create(
        	[
        	'email' => 'elena2708@hotmail.com',
        	'name' => 'Elena Perez',
        	'confirmed' => 1,
        	'password' => $pass
        	]
        );
    }
}
