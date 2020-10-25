<?php

namespace Database\Factories;

use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

class MemberFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Member::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'first_name'   => $this->faker->firstNameMale,
            'last_name'    => $this->faker->firstNameMale,
            'hebrew_name'  => \Faker\Provider\he_IL\Person::firstNameMale(),
            'wife_name'    => $this->faker->firstNameFemale,
            'email'        => $this->faker->safeEmail,
            'home_phone'   => $this->faker->phoneNumber,
            'mobile_phone' => $this->faker->phoneNumber,
            'shtibel'      => $this->faker->randomElement(['JM', 'Parc', 'Avreichim']),
        ];
    }
}
