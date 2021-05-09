<?php

namespace Tests\Feature\Members;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_members_index_page_can_be_rendered()
    {
        $this->loginNewUser();

        $response = $this->get('/members');

        $response->assertStatus(200);
    }
}
