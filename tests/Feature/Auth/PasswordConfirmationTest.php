<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordConfirmationTest extends TestCase
{
    use RefreshDatabase;

    public function test_confirm_password_screen_can_be_rendered()
    {
        $this->actingAs(User::factory()->create());

        $response = $this->get('/confirm-password');

        $response->assertStatus(200);
    }

    public function test_password_can_be_confirmed()
    {
        $this->actingAs(User::factory()->create());

        $response = $this->post('/confirm-password', [
            'password' => 'password',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('auth.password_confirmed_at');
    }

    public function test_password_is_not_confirmed_with_invalid_password()
    {
        $this->actingAs(User::factory()->create());

        $response = $this->post('/confirm-password', [
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors();
        $response->assertSessionMissing('auth.password_confirmed_at');
    }
}
