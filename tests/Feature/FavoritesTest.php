<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class FavoritesTest extends TestCase
{
    use DatabaseMigrations;    

    /** @test */
    public function guests_can_not_favorite_anything()
    {
        // If I post to a "favorite" endpoint
        $this->post('/replies/1/favorite')
            ->assertRedirect('login');
    }


    /** @test */
    public function an_authenticated_user_can_favorite_any_reply()
    {
        $this->signIn();

        $reply = create('App\Reply');

        // If I post to a "favorite" endpoint
        $this->post('/replies/'.$reply->id.'/favorite');
        // It should be recorded on the database.

        $this->assertCount(1, $reply->favorites);
    }

    /** @test */
    public function an_authenticated_user_can_unfavorite_a_reply()
    {
        $this->signIn();

        $reply = create('App\Reply');

        // If I post to a "favorite" endpoint
        $reply->favorite();

        // If I post to a "unfavorite" endpoint
        $this->delete('/replies/'.$reply->id.'/favorite');
        // It should be deleted on the database.
        $this->assertCount(0, $reply->favorites);
    }


    /** @test */
    public function an_authenticated_user_may_only_favorite_a_reply_once()
    {
        $this->withOutExceptionHandling()->signIn();

        $reply = create('App\Reply');

        // If I post to a "favorite" endpoint
        try {

            $this->post('/replies/'.$reply->id.'/favorite');
            $this->post('/replies/'.$reply->id.'/favorite');
            
        } catch (\Exception $e) {
            $this->fail('Did not expect to insert the same record twice.');
        }
        // It should be recorded on the database.

        $this->assertCount(1, $reply->favorites);

    }
}