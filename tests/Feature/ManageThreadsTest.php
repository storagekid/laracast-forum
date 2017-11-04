<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Activity;

class ManageThreadsTest extends TestCase
{
	use DatabaseMigrations;

	/** @test */
    public function unauthecated_may_not_create_threads() {

    	$this->expectException('Illuminate\Auth\AuthenticationException');

    	$this->withOutExceptionHandling()->post('/threads', []);
    }

    /** @test */
    public function unauthenticated_may_not_view_create_thread_form() {

        $this->expectException('Illuminate\Auth\AuthenticationException');

        $this->withOutExceptionHandling()->get('/threads/create')
            ->assertRedirect('login');
    }

    /** @test */
    public function an_authenticated_user_can_create_new_threads()
    {
        // Given we have a signed user
        $this->signIn();

        // When we hit the endpoint to create a new thread
        $thread = create('App\Thread');
        $this->post('/threads', $thread->toArray());

        // Then, when we visit the thread page
        $this->get($thread->path())->assertSee($thread->title)->assertSee($thread->body);
    }

    /** @test */
    public function a_thread_creation_requires_validation()
    {
        // Title is required
        $this->publishThread(['title'=>null])
            ->assertSessionHasErrors('title');

        // Title's length can't be less than 5
        $this->publishThread(['title'=>'pata'])
            ->assertSessionHasErrors('title');

        // Title's length can't be more than 30
        $this->publishThread(['title'=>'patajhfifheifheiuwfhewhfeuiwfheuwfhei'])
            ->assertSessionHasErrors('title');

        // Body is required
        $this->publishThread(['body'=>null])
            ->assertSessionHasErrors('body');

        factory('App\Channel',2)->create();

        // Channel ID is required
        $this->publishThread(['channel_id'=>null])
            ->assertSessionHasErrors('channel_id');

        // Channel ID has to exists on Channels table
        $this->publishThread(['channel_id'=>999])
            ->assertSessionHasErrors('channel_id');
    }

    /** @test */
    public function unauthorised_users_may_not_delete_threads()
    {

        // Guest User Trying to delete threads
        $thread = create('App\Thread');

        $this->delete($thread->path())
            ->assertRedirect('login');


        // Login User Without authoritation Trying to delete threads   
        $this->signIn();

        $this->delete($thread->path())
            ->assertStatus(403);

    }

    /** @test */
    public function a_thread_can_be_deleted_by_authorised_users()
    {
        // Given we have a signed user
        $this->withOutExceptionHandling()->signIn();

        // When we hit the endpoint to delete a thread
        $thread = create('App\Thread', ['user_id'=>auth()->id()]);
        $replies = create('App\Reply', ['thread_id'=>$thread->id], 10);

        $response = $this->json('DELETE', $thread->path());

        $response->assertStatus(204);

        // Then, it shouldn't be on the DB
        $this->assertDatabaseMissing('threads', ['id'=>$thread->id]);
        foreach($replies as $reply) {
            $this->assertDatabaseMissing('replies', ['id'=>$reply->id]);
        }
        $this->assertDatabaseMissing('activities', [
            'subject_id'=>$thread->id,
            'subject_type'=>get_class($thread)
            ]);
        foreach($replies as $reply) {
            $this->assertDatabaseMissing('activities', [
            'subject_id'=>$reply->id,
            'subject_type'=>get_class($reply)
            ]);
        }

        $this->assertEquals(0, Activity::count());
    }

    public function publishThread($overrrides){

        // Given we have a signed user
        $this->withExceptionHandling()->signIn();

        $thread = make('App\Thread', $overrrides);

        // When we hit the endpoint to create a new thread
        return $this->post('/threads', $thread->toArray());

    }

}
