<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UsersTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_index_page_can_be_rendered()
    {
        $this->loginNewUser();

        $response = $this->get('/users');

        $response->assertStatus(200);
    }
}
