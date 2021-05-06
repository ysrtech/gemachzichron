<?php

namespace Database\Factories;

use App\Models\Member;
use Faker\Provider\he_IL\Person;
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
            'first_name'        => $this->faker->firstNameMale(),
            'last_name'         => $this->faker->lastName(),
            'hebrew_name'       => Person::firstNameMale(),
            'wife_name'         => $this->faker->firstNameFemale(),
            'address'           => $this->faker->streetAddress(),
            'city'              => $this->faker->randomElement(['Montreal QC', 'Outremont QC', 'Cote-Saint-Luc QC']),
            'postal_code'       => $this->faker->postcode(),
            'email'             => $this->faker->safeEmail(),
            'home_phone'        => $this->faker->phoneNumber(),
            'mobile_phone'      => $this->faker->phoneNumber(),
            'wife_mobile_phone' => $this->faker->phoneNumber(),
            'shtibel'           => $this->faker->randomElement(['Zichron YY', 'Bais Yakov', 'Avreichim', 'CSL']),
        ];
    }
}
