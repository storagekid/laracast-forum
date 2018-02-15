<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RepliesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$threads = \App\Thread::all();
    	$users = \App\User::all();
    	foreach ($threads as $thread) {
    		$hasReplies = mt_rand(0,5);
    		if ($hasReplies || ($thread->user_id > 20)) {
    			$repliesNumber = mt_rand(2,5);
    			while ($repliesNumber--) {
    				factory('App\Reply')->create([
    					'user_id' => $users[mt_rand(0,count($users)-1)],
    					'thread_id' => $thread->id,
    				]);
    			}
    			if ($thread->user_id > 20) {
    				$repliesNumber = mt_rand(1,4);
    				while ($repliesNumber--) {
    					factory('App\Reply')->create([
    						'user_id' => mt_rand(20,21),
    						'thread_id' => $thread->id,
    					]);
    				}
    			}
    		}
    	}
    }
}
