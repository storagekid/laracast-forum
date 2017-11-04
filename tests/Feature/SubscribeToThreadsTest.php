<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class SubscribeToThreadsTest extends TestCase
{
    use DatabaseMigrations;    

    public function setUp() {

        parent::setUp();

        $this->thread = create('App\Thread');
    }


    /** @test */
    public function a_user_can_subscribe_to_threads()
    {
        $this->signIn();

        $this->withOutExceptionHandling()->post($this->thread->path().'/subscriptions');
        $this->assertCount(1, $this->thread->subscriptions);
    }


}




