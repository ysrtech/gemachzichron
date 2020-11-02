<?php

namespace Database\Factories;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Subscription::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'type' => $this->faker->randomElement([Subscription::TYPE_MEMBERSHIP, Subscription::TYPE_LOAN_PAYMENT]),
            'amount' => $this->faker->numberBetween(20,500),
            'start_date' => $this->faker->dateTimeBetween('-10 years', '+10 years'),
            'recurrences' => $this->faker->numberBetween(1,500),
            'frequency' => $this->faker->randomElement([Subscription::FREQUENCY_MONTHLY, Subscription::FREQUENCY_BIMONTHLY]),
            'process_day' => $this->faker->numberBetween(1,28)
        ];
    }
}
