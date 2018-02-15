<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class LockThreads extends TestCase
{
	use DatabaseMigrations;

	/** @test */
	function non_administrators_may_not_lock_threads() {
		$this->signIn();
		$thread = create('App\Thread', ['user_id' => auth()->id()]);
		$this->post(route('locked-threads.store', $thread))->assertStatus(403);

		$this->assertFalse($thread->fresh()->locked);
	}

	/** @test */
	function administrators_can_lock_threads() {
		$user = factory('App\User')->states('administrator')->create();
		$this->signIn($user);
		$thread = create('App\Thread', ['user_id' => auth()->id()]);
		$this->post(route('locked-threads.store', $thread))->assertStatus(200);
		$this->assertTrue($thread->fresh()->locked);
	}

	/** @test */
	function administrators_can_unlock_threads() {
		$user = factory('App\User')->states('administrator')->create();
		$this->signIn($user);
		$thread = create('App\Thread', ['user_id' => auth()->id(), 'locked' => true]);
		$this->delete(route('locked-threads.destroy', $thread))->assertStatus(200);
		$this->assertFalse($thread->fresh()->locked);
	}

	/** @test */
	function  once_locked_a_thread_may_not_recieve_new_replies() {
		$this->signIn();
		$thread = create('App\Thread', ['locked' => true]);
		$this->post($thread->path() . '/replies', [
			'body' => 'Hohenheim',
			'user_id' => auth()->id(),
		])->assertStatus(422);
	}
}