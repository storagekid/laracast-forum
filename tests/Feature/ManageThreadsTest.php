<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Activity;
use App\Rules\Recaptcha;

class ManageThreadsTest extends TestCase
{
	use DatabaseMigrations;

    public function setUp() {
        parent::setUp();

        app()->singleton(Recaptcha::class, function() {
            return \Mockery::mock(Recaptcha::class, function($m) {
                $m->shouldReceive('passes')->andReturn(true);
            });
        });
    }

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
    public function an_authenticated_user_must_confirm_their_email_address_before_creating_threads() {
        $user = factory('App\User')->states('unconfirmed')->create();
        $this->signIn($user);
        $this->publishThread([])
            ->assertRedirect(route('home'))
            ->assertSessionHas('flash');
    }

    /** @test */
    public function an_authenticated_user_can_create_new_threads()
    {
        // Given we have a signed user
        $this->signIn();
        // When we hit the endpoint to create a new thread
        $thread = make('App\Thread');
        $this->post('/threads', $thread->toArray() + ['g-recaptcha-response'=>'test']);

        // Then, when we visit the thread page
        $this->get($thread->path())->assertSee($thread->title)->assertSee($thread->body);
    }

    /** @test */
    public function a_thread_creation_requires_validation()
    {
        $this->signIn();
        // auth()->user()->confirmed = true;
        // Title is required
        $this->publishThread(['title'=>null])
            // ->assertStatus(422);
            ->assertSessionHasErrors('title');

        // Title's length can't be less than 5
        $this->publishThread(['title'=>'pata'])
            // ->assertStatus(422);
            ->assertSessionHasErrors('title');

        // Title's length can't be more than 255
        $this->publishThread(['title'=> str_random(256)])
            // ->assertStatus(422);
            ->assertSessionHasErrors('title');

        // Body is required
        $this->publishThread(['body'=>null])
            // ->assertStatus(422);
            ->assertSessionHasErrors('body');

        factory('App\Channel',2)->create();

        // Channel ID is required
        $this->publishThread(['channel_id'=>null])
            // ->assertStatus(422);
            ->assertSessionHasErrors('channel_id');

        // Channel ID has to exists on Channels table
        $this->publishThread(['channel_id'=>999])
            // ->assertStatus(422);
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

    /** @test */
    function a_thread_requires_recaptcha_verification() {
        unset(app()[Recaptcha::class]);
        $this->signIn();
        $this->publishThread(['g-recaptcha-response'=>'test'])
            ->assertSessionHasErrors('g-recaptcha-response');
    }

    /** @test */
    function a_thread_with_a_title_that_ends_in_a_number_should_generate_the_proper_slug() 
    {
        $this->signIn();
        $thread = create('App\Thread', ['title'=>'Foo Title 34']);
        $thread2 = $this->postJson('/threads', $thread->toArray() + ['g-recaptcha-response'=>'test'])->json();
        $this->assertEquals("foo-title-34-{$thread2['id']}", $thread2['slug']);
    }

    /** @test */
    public function a_thread_requires_a_unique_slug()
    {
        $this->signIn();
        $thread = create('App\Thread', ['title'=>'Foo Title']);
        $this->assertEquals($thread->fresh()->slug, 'foo-title');
        $thread2 = $this->withOutExceptionHandling()->postJson('/threads', $thread->toArray() + ['g-recaptcha-response'=>'test'])->json();
        $this->assertTrue(\App\Thread::whereSlug("foo-title-{$thread2['id']}")->exists());
    }

    public function publishThread($overrrides){

        // Given we have a signed user
        if (! auth()->check()) {
            $this->withExceptionHandling()->signIn();
        }
        $thread = make('App\Thread', $overrrides);
        // When we hit the endpoint to create a new thread
        return $this->post('/threads', $thread->toArray());

    }

}
