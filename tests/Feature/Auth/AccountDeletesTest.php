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
        $this->actingAs($user = User::factory()->create());

        $this->delete('/profile');

        $this->assertAuthenticated();
        $this->assertNotNull($user->fresh());
    }

    public function test_user_can_delete_account_with_password_confirmation()
    {
        $this->actingAs($user = User::factory()->create());

        $this->post('/confirm-password', [
            'password' => 'password'
        ]);

        $response = $this->delete('/profile');

        $response->assertRedirect('/');
        $this->assertGuest();
        $this->assertNull($user->fresh());
    }
}
