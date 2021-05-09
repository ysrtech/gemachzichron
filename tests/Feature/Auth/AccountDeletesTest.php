<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountDeletesTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_delete_account_without_password_confirmation()
    {
        $user = $this->loginNewUser();

        $this->delete('/account');

        $this->assertAuthenticated();
        $this->assertNotNull($user->fresh());
    }

    public function test_user_can_delete_account_with_password_confirmation()
    {
        $user = $this->loginNewUser();

        $this->post('/confirm-password', [
            'password' => 'password'
        ]);

        $response = $this->delete('/account');

        $response->assertRedirect('/');
        $this->assertGuest();
        $this->assertNull($user->fresh());
    }
}
