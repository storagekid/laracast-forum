<?php

namespace Tests\Unit;

use Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ReplyTest extends TestCase
{

	use DatabaseMigrations;
    /** @test */
    public function reply_has_an_owner()
    {
    	$this->signIn();
        $reply = create('App\Reply', ['user_id'=>auth()->id()]);

        $this->assertInstanceOf('App\User', $reply->owner);
    }

    /** @test */
    public function a_reply_knows_if_it_was_just_publish()
    {
    	$this->signIn();
        $reply = create('App\Reply', ['user_id'=>auth()->id()]);
       	$this->assertTrue($reply->wasJustPublished());

       	$reply->created_at = Carbon::now()->subMonth();
       	$this->assertFalse($reply->wasJustPublished());
    }

    /** @test */
    public function it_can_detect_all_mentioned_users_in_the_body()
    {
        $this->signIn();
        $reply = make('App\Reply', [
            'body' => '@JaneDoe and @FrankDoe look at this!!!'
        ]);
        $this->assertCount(2, $reply->mentionedUsers());
        $this->assertEquals(['JaneDoe', 'FrankDoe'], $reply->mentionedUsers());
    }
}
