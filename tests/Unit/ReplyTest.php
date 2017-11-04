<?php

namespace Tests\Unit;

use Tests\TestCase;
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
}
