<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountUpdatesTest extends TestCase
{
    use RefreshDatabase;

    public function test_account_page_can_be_rendered()
    {
        $this->loginNewUser();

        $response = $this->get(route('account.show'));

        $response->assertStatus(200);
    }

    public function test_user_can_update_account_information()
    {
        $user = $this->loginNewUser();

        $this->put('/account', [
            'name'  => 'Updated Name',
            'email' => 'updated-email@example.com',
        ]);

        $this->assertEquals('Updated Name', $user->name);
        $this->assertEquals('updated-email@example.com', $user->email);
    }

    public function test_user_email_has_to_be_unique()
    {
        $this->loginNewUser();

        $anotherUser = User::factory()->create([
            'email' => 'another-user@example.com'
        ]);

        $response = $this->put('/account', [
            'email' => $anotherUser->email,
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_user_can_update_password()
    {
        $user = $this->loginNewUser();

        $response = $this->put('/account/password', [
            'current_password'      => 'wrong-password',
            'password'              => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertSessionHasErrors('current_password');

        $response = $this->put('/account/password', [
            'current_password'      => 'password',
            'password'              => 'new-password',
            'password_confirmation' => 'not-matched',
        ]);

        $response->assertSessionHasErrors('password');

        $response = $this->put('/account/password', [
                'current_password'      => 'password',
                'password'              => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response->assertSessionDoesntHaveErrors();

        $this->post('/logout');

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'new-password',
        ]);

        $this->assertAuthenticated();
    }
}
