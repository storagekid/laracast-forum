<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ThreadsTest extends TestCase
{
    use DatabaseMigrations;    

    public function setUp() {
        parent::setUp();
        $this->thread = create('App\Thread');
    }


    /** @test */
    public function a_user_can_view_all_threads()
    {
        $response = $this->withOutExceptionHandling()->get('/threads');
        $response->assertStatus(200);
        $response->assertSee($this->thread->title);
        $response->assertSee($this->thread->body);
    }

    /** @test */
    public function a_user_can_view_a_single_thread()
    {
        $response = $this->get($this->thread->path());
        $response->assertSee($this->thread->title);
        $response->assertSee($this->thread->body);
    }

    /** @test */
    public function a_user_can_filter_threads_according_to_a_channel()
    {
        $channel = create('App\Channel');
        $threadInChannel = create('App\Thread',['channel_id'=>$channel->id]);
        $threadNotInChannel = create('App\Thread');
        $this->get('/threads/'.$channel->slug)
            ->assertSee($threadInChannel->body)
            ->assertDontSee($threadNotInChannel->body);
    }

    /** @test */
    public function a_user_can_filter_threads_by_any_username()
    {
        $this->signIn(create('App\User', ['name'=>'JohnDoe']));
        $threadByJohn = create('App\Thread', ['user_id'=>auth()->id()]);
        $threadNotByJohn = create('App\Thread');
        $this->get('/threads?by=JohnDoe')
            ->assertSee($threadByJohn->body)
            ->assertDontSee($threadNotByJohn->body);
    }

    /** @test */
    public function a_user_can_filter_threads_by_popularity()
    {
        $threadWithTwoReplies = create('App\Thread');
        create('App\Reply', ['thread_id'=>$threadWithTwoReplies->id], 2);
        $threadWithThreeReplies = create('App\Thread');
        create('App\Reply', ['thread_id'=>$threadWithThreeReplies->id], 3);
        $threadWithZeroReplies = $this->thread;
       //When I filter threads by popularity 
       $response = $this->getJson('/threads?popularity=1')->json();
       // They should be returned from most replies to least
       $this->assertEquals([3,2,0], array_column($response['data'], 'replies_count'));

    }

    /** @test */
    public function a_user_can_filter_threads_by_those_that_have_no_replies()
    {
        // Having one thread with no replies ($this->thread)
        // and another with one reply.
        $threadWithOneReply = create('App\Thread');
        create('App\Reply', ['thread_id'=>$threadWithOneReply->id], 1); 
       //When I filter threads by unanswered 
       $response = $this->getJson('/threads?unreplied')->json();
       // They should be returned only the unanswered thread
       $this->assertCount(1, $response['data']);
    }

    /** @test */
    public function a_user_can_request_all_replies_for_a_given_thread()
    {
        $thread = create('App\Thread');
        $replies = create('App\Reply', ['thread_id'=>$thread->id], 10);
        $response = $this->getJson($thread->path() . '/replies')->json();
        $this->assertEquals(10, $response['total']);
    }

    /** @test */
    public function a_thread_can_be_subscribed_to()
    {
        $user = $this->signIn();
        $this->thread->subscribe();
        $this->assertEquals(1,$this->thread->subscriptions()->where('user_id', auth()->id())->count());

    }

    /** @test */
    public function a_thread_can_be_unsubscribed_from()
    {
        $user = $this->signIn();
        $this->thread->subscribe();
        $this->thread->unsubscribe();
        $this->assertCount(0,$this->thread->subscriptions);

    }
    /** @test */
    public function a_thread_records_each_visit_with_database_approach()
    {
        $this->assertSame(0, $this->thread->fresh()->visits_count);
        $this->call('GET', $this->thread->path());
        $this->assertEquals(1, $this->thread->fresh()->visits_count);
    }
}




