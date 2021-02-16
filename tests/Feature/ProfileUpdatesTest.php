<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileUpdatesTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_can_be_rendered()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('profile.show'));

        $response->assertStatus(200);
    }

    public function test_user_can_update_profile_information()
    {
        $user = User::factory()->create();

        $this
            ->actingAs($user)
            ->put('/profile', [
                'name'  => 'Updated Name',
                'email' => 'updated-email@example.com',
            ]);

        $this->assertEquals('Updated Name', $user->name);
        $this->assertEquals('updated-email@example.com', $user->email);
    }

    public function test_user_email_has_to_be_unique()
    {
        $user = User::factory()->create();

        $newUser = User::factory()->create([
            'email' => 'new-user@example.com'
        ]);

        $this
            ->actingAs($user)
            ->put('/profile', [
                'email' => $newUser->email,
            ])
            ->assertSessionHasErrors('email');
    }

    public function test_user_can_update_password()
    {
        $user = User::factory()->create();

        $this
            ->actingAs($user)
            ->put('/profile/password', [
                'current_password'      => 'wrong-password',
                'password'              => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertSessionHasErrors('current_password');

        $this
            ->actingAs($user)
            ->put('/profile/password', [
                'current_password'      => 'password',
                'password'              => 'new-password',
                'password_confirmation' => 'not-matched',
            ])
            ->assertSessionHasErrors('password');

        $this
            ->actingAs($user)
            ->put('/profile/password', [
                'current_password'      => 'password',
                'password'              => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $this->post('/logout');

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'new-password',
        ]);

        $this->assertAuthenticated();
    }

    public function test_user_delete_his_profile()
    {
        $user = User::factory()->create();

        $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password'
            ])
            ->assertRedirect('/');

        $this->assertGuest();

        $this->assertDeleted('users', $user->toArray());
    }
}
