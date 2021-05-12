<?php

namespace Tests\Feature;

use App\Models\PlanType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PlanTypesTest extends TestCase
{
    use RefreshDatabase;

    public function test_plan_types_index_page_can_be_rendered()
    {
        $this->loginNewUser();

        $response = $this->get('/plan-types');

        $response->assertStatus(200);
    }

    public function test_plan_type_can_be_added()
    {
        $this->loginNewUser();

        $response = $this->post('/plan-types', [
            'name'  => null,
        ]);

        $response->assertSessionHasErrors('name');

        $this->post('/plan-types', [
            'name'  => 'New Plan Type',
        ]);

        $this->assertDatabaseHas('plan_types', ['name' => 'New Plan Type']);
    }

    public function test_plan_type_can_be_updated()
    {
        $this->loginNewUser();

        $this->post('/plan-types', [
            'name'  => 'New Plan Type',
        ]);

        $id = PlanType::firstWhere('name', 'New Plan Type')->id;

        $this->put('/plan-types/' . $id, [
            'name'  => 'Updated Plan Type',
        ]);

        $this->assertDatabaseMissing('plan_types', ['name' => 'New Plan Type']);
        $this->assertDatabaseHas('plan_types', ['name' => 'Updated Plan Type']);
    }
}
