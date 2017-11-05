<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UserTest extends TestCase
{

	use DatabaseMigrations;
    /** @test */
    public function a_user_can_fetch_their_most_recent_reply()
    {
    	$user = create('App\User');
    	$this->signIn($user);
        $reply = create('App\Reply', ['user_id'=>auth()->id()]);

        $this->assertEquals($user->lastReply->id, $reply->id); 
    }
}
