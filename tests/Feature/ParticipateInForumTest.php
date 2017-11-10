<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ParticipateInForumTest extends TestCase
{
	use DatabaseMigrations;

	/** @test */
    public function unauthenticated_may_not_post_replies() {

        $thread = create('App\Thread');

    	$this->expectException('Illuminate\Auth\AuthenticationException');

    	$this->withOutExceptionHandling()->post($thread->path().'/replies', []);
    }
    /** @test */
    public function an_authenticated_user_may_participate_in_forum_threads() {

    	// Given we have an authenticated user
    	$this->signIn();

    	// And an existing thread
    	$thread = create('App\Thread');

    	// When the user adds a reply to the thread
    	$reply = create('App\Reply');

    	$this->post($thread->path().'/replies', $reply->toArray());

    	// Then his/her reply should be included on the page
    	$this->assertDatabaseHas('replies',['id'=>$reply['id']]);
        $this->assertEquals(1, $thread->fresh()->replies_count);

        $thread->replies->each->delete();
        $this->assertEquals(0, $thread->fresh()->replies_count);

    }

    /** @test */
    public function a_reply_post_requires_validation()
    {
        $thread = create('App\Thread');
        // Body is required
        $this->publishReply($thread->path(),['body'=>null,'thread_id'=>$thread->id])
            // ->assertSessionHasErrors('body');
            ->assertStatus(302);

        // Body has to be at least 5 characters long
        $this->publishReply($thread->path(),['body'=>'papa','thread_id'=>$thread->id])
            // ->assertSessionHasErrors('body');
            ->assertStatus(302);

    }

    /** @test */
    public function unauthorised_users_can_not_delete_replies()
    {
        $reply = create('App\Reply');
        
        $this->delete('/replies/'.$reply->id)
            ->assertRedirect('login');

        $this->signIn();

        $this->delete('/replies/'.$reply->id)
            ->assertStatus(403);

    }

    /** @test */
    public function authorised_users_can_delete_replies_they_own()
    {

        $this->withOutExceptionHandling()->signIn();
        $reply = create('App\Reply', ['user_id'=>auth()->id()]);

        $this->delete('/replies/'.$reply->id);
            
        $this->assertDatabaseMissing('replies',['id'=>$reply->id]);

    }

    /** @test */
    public function unauthorised_users_can_not_update_replies()
    {
        $reply = create('App\Reply');

        $body = 'You\'ve been changed';
        
        $this->patch('/replies/'.$reply->id, ['body' => $body])
            ->assertRedirect('login');

        $this->signIn();

        $this->patch('/replies/'.$reply->id, ['body' => $body])
            ->assertStatus(403);
    }

    /** @test */
    public function authorised_users_can_delete_replies_they_made()
    {

        $this->withOutExceptionHandling()->signIn();
        $reply = create('App\Reply', ['user_id'=>auth()->id()]);

        $body = 'You\'ve been changed';

        $this->patch('/replies/'.$reply->id, ['body' => $body]);
            
        $this->assertDatabaseHas('replies',['id'=>$reply->id, 'body'=>$body]);

    }

    /** @test */
    public function replies_that_contain_spam_may_not_be_created()
    {
        $this->withExceptionHandling()->signIn();
        $thread = create('App\Thread');
        $reply = make('App\Reply', [
            'body' => 'Yahoo Customer Support'
        ]);
        // $this->expectException(\Exception::class);
        $this->json('post',$thread->path().'/replies', $reply->toArray())
            ->assertStatus(422);
    }
    /** @test */
    public function a_user_can_only_reply_once_per_minute()
    {
        $this->withExceptionHandling()->signIn();
        $thread = create('App\Thread');
        $reply = make('App\Reply', [
            'body' => 'My simple Reply'
        ]);
        $this->post($thread->path().'/replies', $reply->toArray())
            ->assertStatus(200);
        $this->post($thread->path().'/replies', $reply->toArray())
            ->assertStatus(429);
    }

    public function publishReply($path,$overrrides){

        // Given we have a signed user
        $this->withExceptionHandling()->signIn();

        $reply = make('App\Reply', $overrrides);

        // When we hit the endpoint to create a new thread
        return $this->post($path.'/replies', $reply->toArray());

    }

}
