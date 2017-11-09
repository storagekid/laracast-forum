<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ThreadWasUpdated;

class ThreadTest extends TestCase
{
	use DatabaseMigrations;

	public function setUp() {

		parent::setUp();

        $this->signIn();

        $this->thread = create('App\Thread', ['user_id'=>auth()->id()]);

	}

    /** @test */
    public function a_thread_can_make_a_string_path()
    {
        $this->assertEquals('/threads/'.$this->thread->channel->slug.'/'.$this->thread->id,$this->thread->path());
    }

    /** @test */
    public function thread_has_an_owner()
    {
        $this->assertInstanceOf('App\User', $this->thread->user);
    }

    /** @test */
    public function thread_has_replies()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->thread->replies);
    }

    /** @test */
    public function a_thread_can_add_a_reply()
    {

        $this->thread->addReply([

        	'body' => 'Foobar',
        	'user_id' => 1

        ]);

        $this->assertCount(1, $this->thread->replies);
    }

    /** @test */
    public function a_thread_notifies_all_subscribers_when_a_reply_is_added()
    {
        // Test with real notification sending
        $this->thread->subscribe()->addReply([
            'body' => 'Foobar',
            'user_id' => create('App\User')->id,
        ]);
        $this->assertCount(1, auth()->user()->notifications);
    
        // Test with fake notification
        Notification::fake();
        $this->thread->addReply([
            'body' => 'Foobar',
            'user_id' => create('App\User')->id,
        ]);
        Notification::assertSentTo(auth()->user(), ThreadWasUpdated::class);
    }

    /** @test */
    public function a_thread_belongs_to_a_channel()
    {
        $this->assertInstanceOf('App\Channel', $this->thread->channel);
    }
    
    /** @test */
    public function a_thread_can_be_subscribe_and_unsubscribed_from()
    {
        $user = $this->signIn();
        $this->thread->subscribe();
        $this->assertCount(1,$this->thread->subscriptions);
        $this->thread->unsubscribe();
        $this->assertCount(0,$this->thread->fresh()->subscriptions);
    }
    /** @test */
    public function it_knows_if_an_authenticated_user_is_subscribed_to_it_and_viceversa()
    {
        $user = $this->signIn();
        $this->assertFalse($this->thread->isSubscribeTo);
        $this->thread->subscribe();
        $this->assertTrue($this->thread->fresh()->isSubscribeTo);
        $this->thread->unsubscribe();
        $this->assertFalse($this->thread->fresh()->isSubscribeTo);
    }

    /** @test */
    public function a_thread_can_check_if_the_authenticated_user_has_read_all_replies()
    {
        $user = auth()->user();
        $this->assertTrue($this->thread->hasUpdatesFor($user));
        // $this->get($this->thread->path());
        $user->readed($this->thread);
        $this->assertFalse($this->thread->hasUpdatesFor($user));
        $this->thread->updated_at = \Carbon\Carbon::now()->addMonth();
        $this->assertTrue($this->thread->hasUpdatesFor($user));
    }
    /** @test */
    public function a_thread_records_each_visit()
    {
        $this->thread->resetVisits('threads');
        $this->assertSame(0, $this->thread->visits('threads'));
        $this->thread->recordVisit('threads');
        $this->assertEquals(1, $this->thread->visits('threads'));
        $this->thread->recordVisit('threads');
        $this->assertEquals(2, $this->thread->visits('threads'));
    }
}
