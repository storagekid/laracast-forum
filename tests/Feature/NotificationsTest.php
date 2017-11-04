<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Notifications\DatabaseNotification;

class NotificationsTest extends TestCase
{
	use DatabaseMigrations;

	public function setUp() {
		parent::setUp();
		$this->signIn();
	}

    /** @test */
    public function a_notification_is_prepared_when_a_subscribed_thread_recieves_a_new_reply_that_is_not_by_the_current_user()
    {
        $thread = create('App\Thread')->subscribe();
        $this->assertCount(0, auth()->user()->notifications);
        // If we leave a reply there shouldn't be any notification for us
        $thread->addReply([
            'user_id' => auth()->id(),
            'body' => 'Somre Reply here.',
        ]);
		$this->assertCount(0, auth()->user()->fresh()->notifications);

		// But if another user replies the thread we must recieve a notification
		$thread->addReply([
            'user_id' => 2,
            'body' => 'Somre Reply here.',
        ]);
		$this->assertCount(1, auth()->user()->fresh()->notifications);
    }

    /** @test */
    public function a_user_can_mark_a_notification_as_read() {
    	create(DatabaseNotification::class);
    	$user = auth()->user();
    	$id = $user->unreadNotifications->first()->id;
		$this->assertCount(1, $user->unreadNotifications);
		$this->delete("/profiles/{$user->name}/notifications/{$id}");
		$this->assertCount(0, $user->fresh()->unreadNotifications);
    }

    /** @test */
    public function a_user_can_fetch_unread_notifications() {
    	create(DatabaseNotification::class);
    	$response = $this->getJson("/profiles/".auth()->user()->name."/notifications")->json();
    	$this->assertCount(1, $response);

    }
}



