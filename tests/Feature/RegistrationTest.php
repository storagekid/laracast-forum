<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\Mail\PleaseConfirmYourEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class RegistrationTest extends TestCase
{
	use DatabaseMigrations;

	public function setUp() {
		parent::setUp();
		Mail::fake();
	}
	/** @test */
    public function a_confirmation_email_is_sent_upon_registation() {
        // Fake Registraion event that took place when a user successfully send a form to singup
        // Mail::fake();
        event(new Registered(create('App\User')));
        Mail::assertSent(PleaseConfirmYourEmail::class);
    }
    /** @test */
    public function users_can_fully_confirm_their_email_addresses() {
    	// Mail::fake();
        $this->post(route('register'), [
        	'name' => 'John',
        	'email' => 'john@example.com',
        	'password' => 'foobar',
        	'password_confirmation' => 'foobar'
        ]);
        $user = User::whereName('John')->first();
        $this->assertFalse($user->confirmed);
        $this->assertNotNull($user->confirmation_token);

        $response = $this->get(route('register.confirm', ['token' => $user->confirmation_token]))
            ->assertRedirect(route('home'));
        $this->assertTrue($user->fresh()->confirmed);
    }
    /** @test */
    public function confirming_an_invalid_token() {
        $this->get(route('register.confirm', ['token' => 'invalid']))
            ->assertRedirect(route('home'))
            ->assertSessionHas('flash', 'Unknown token');
    }
}
