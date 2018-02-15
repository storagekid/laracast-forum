<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Storage;

class BestReplyTest extends TestCase
{
    use DatabaseMigrations;    

    public function setUp() {
        parent::setUp();
        $this->thread = create('App\Thread');
    }
    /** @test */
    public function a_thread_creator_may_mark_any_reply_as_the_best_reply()
    {   
        $this->signIn();
        $thread = create('App\Thread', ['user_id' => auth()->id()]);
        $replies = create('App\Reply', ['thread_id' => $thread->id], 2);
        $this->assertFalse($replies[1]->isBest());
        $this->withOutExceptionHandling()->postJson(route('best-replies.store', $replies[1]));
        $this->assertTrue($replies[1]->fresh()->isBest());
    }

    /** @test */
    function only_the_thread_creator_may_mark_a_reply_as_best_one() {
        $this->signIn();
        $thread = create('App\Thread', ['user_id' => auth()->id()]);
        $replies = create('App\Reply', ['thread_id' => $thread->id], 2);
        $this->signIn(create('App\User'));
        $this->postJson(route('best-replies.store', $replies[1]))->assertStatus(403);
        $this->assertFalse($replies[1]->fresh()->isBest());
    }

    /** @test */
    function if_a_best_reply_is_deleted_then_the_thread_is_properly_updated_to_reflect_that() {
        $this->signIn();
        $reply = create('App\Reply',['user_id' => auth()->id()]);
        $reply->thread->setBestReply($reply);
        $this->assertEquals($reply->thread->best_reply_id,$reply->id);
        $this->deleteJson(route('replies.destroy', $reply));
        $this->assertDatabaseMissing('replies',['id' => $reply->id]);
        $this->assertNull($reply->thread->fresh()->best_reply_id);
    }
}




