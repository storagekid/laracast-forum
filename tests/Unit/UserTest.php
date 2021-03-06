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
    /** @test */
    public function a_user_can_determine_their_avatar_path()
    {
    	$user = create('App\User');
    	$this->assertEquals(asset('avatars/default.jpg'), $user->avatar_path);
    	$user->avatar_path = 'avatars/me.jpg';
    	$this->assertEquals(asset('avatars/me.jpg'), $user->avatar_path);
    }
}
