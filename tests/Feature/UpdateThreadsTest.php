<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateThreadsTest extends TestCase
{
	use RefreshDatabase;

	public function setUp() {
		parent::setUp();
		$this->signIn();
	}

    /** @test */ 
    function a_thread_requires_a_title_and_a_body_to_be_updated() {
        $thread = create('App\Thread', ['user_id' => auth()->id()]);
        $this->patch($thread->path(), [
            'title' => 'Changed title',
        ])->assertSessionHasErrors('body');
        $this->patch($thread->path(), [
            'body' => 'Changed title',
        ])->assertSessionHasErrors('title');
    }

    /** @test */ 
    function unauthorize_users_may_not_update_threads() {
        $thread = create('App\Thread', ['user_id' => create('App\User')->id]);
        $this->patch($thread->path(), [])->assertStatus(403);
    }
    
    /** @test */ 
    function a_thread_can_be_updated_by_its_creator() {
        $this->withOutExceptionHandling();
        $thread = create('App\Thread', ['user_id' => auth()->id()]);
        $this->patch($thread->path(), [
            'title' => 'Changed title',
            'body' => 'Updated body',
        ]);
        tap($thread->fresh(), function($thread) {
            $this->assertEquals('Changed title', $thread->title);
            $this->assertEquals('Updated body', $thread->body);
        });
    }
}
