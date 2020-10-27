<?php

namespace Database\Factories;

use App\Models\Dependent;
use Illuminate\Database\Eloquent\Factories\Factory;

class DependentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Dependent::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'first_name'  => $this->faker->firstNameMale,
            'last_name'   => $this->faker->firstNameMale,
            'hebrew_name' => \Faker\Provider\he_IL\Person::firstNameMale(),
            'dob'         => $this->faker->dateTimeBetween('-18 years')
        ];
    }
}
