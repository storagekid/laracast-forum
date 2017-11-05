<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MentionUsersTest extends TestCase
{
	use DatabaseMigrations;
	/** @test */
    public function mention_users_in_a_reply_are_notified() {
    	$john = create('App\User', ['name'=>'JohnDoe']);
    	$this->withOutExceptionHandling()->signIn($john);
    	$jane = create('App\User', ['name'=>'JaneDoe']);

    	$thread = create('App\Thread');
    	$reply = make('App\Reply', [
    		'user_id' => $john->id,
    		'body' => '@JaneDoe and @FrankDoe look at this!!!'
    	]);

        $this->json('post',$thread->path().'/replies', $reply->toArray());

        $this->assertCount(1, $jane->notifications);

    }
}
