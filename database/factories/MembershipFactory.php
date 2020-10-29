<?php

namespace Database\Factories;

use App\Models\Membership;
use App\Models\PlanType;
use Illuminate\Database\Eloquent\Factories\Factory;

class MembershipFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Membership::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $membershipType = $this->faker->randomElement([
            Membership::TYPE_MEMBERSHIP,
            Membership::TYPE_PEKUDON
        ]);

        return [
            'type' => $membershipType,
            'plan_type_id' => $membershipType == Membership::TYPE_MEMBERSHIP
                ? PlanType::inRandomOrder()->first()->id
                : null
        ];
    }
}
