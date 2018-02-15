<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ThreadsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$channels = \App\Channel::all();
    	$users = \App\User::all();
    	foreach ($users as $user) {
    		$hasThreads = mt_rand(0,3);
    		if ($hasThreads || ($user->id > 20)) {
    			$threadNumber = mt_rand(1,5);
    			while ($threadNumber--) {
    				$thread = factory('App\Thread')->create([
    					'user_id' => $user->id,
    					'channel_id' => $channels[mt_rand(0,count($channels)-1)],
    				]);
                    $thread->subscribe($user->id);
    			}
    		}
    	}
    }
}
