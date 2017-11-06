<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Storage;

class AddAvatarTest extends TestCase
{
    use DatabaseMigrations;    

    public function setUp() {

        parent::setUp();

        $this->thread = create('App\Thread');

    }
    /** @test */
    public function only_authenticated_users_can_upload_avatars()
    {   
        // $this->withOutExceptionHandling()->signIn();
        $this->json('post','/api/users/1/avatar')
            ->assertStatus(401);
    }
    /** @test */
    public function a_valid_avatar_must_be_provided()
    {   
        $this->withExceptionHandling()->signIn();
        $this->json('post','/api/users/'.auth()->id().'/avatar', [
            'avatar' => 'not an image',
        ])->assertStatus(422);
    }
    /** @test */
    public function a_user_may_add_an_avatar_to_their_profile()
    {   
        $this->signIn();
        Storage::fake('public');
        $this->json('post','/api/users/'.auth()->id().'/avatar', [
            'avatar' => $file = UploadedFile::fake()->image('avatar.jpg'),
        ]);
        $this->assertEquals('avatars/'.$file->hashName(), auth()->user()->avatar_path);
        Storage::disk('public')->assertExists('avatars/'.$file->hashName());
    }
}




