<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ProfilesTest extends TestCase
{
    use DatabaseMigrations;    

    /** @test */
    public function a_user_has_a_profile()
    {
        $this->withOutExceptionHandling()->signIn();
        $user = auth()->user();

        // If I post to a "favorite" endpoint
        $this->get('/profiles/'.$user->name)
            ->assertSee($user->name);
    }

    /** @test */
    public function profiles_display_all_threads_created_by_the_user()
    {
        $this->withOutExceptionHandling()->signIn();
        $user = auth()->user();
        $thread = create('App\Thread', ['user_id'=>$user->id]);

        // If I post to a "favorite" endpoint
        $this->get('/profiles/'.$user->name)
            ->assertSee($thread->title);
    }

}