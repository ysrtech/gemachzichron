<?php

namespace Database\Factories;

use App\Models\Member;
use App\Models\PlanType;
use Faker\Generator;
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
        $membership = $this->faker->boolean();

        $membershipType = $this->faker->randomElement([
            Member::TYPE_MEMBERSHIP,
            Member::TYPE_PEKUDON
        ]);

        return [
            'title'             => $this->faker->randomElement(['Rabbi & Mrs.', 'Mr. & Mrs.', 'Rabbi & Reb.', 'Rabbi', 'Mr.', 'Mrs.', 'Reb.']),
            'first_name'        => $this->faker->firstNameMale(),
            'last_name'         => $this->faker->lastName(),
            'hebrew_first_name' => $this->hebrewFaker()->firstNameMale(),
            'hebrew_last_name'  => $this->hebrewFaker()->lastName(),
            'wife_name'         => $this->faker->firstNameFemale(),
            'address'           => $this->faker->streetAddress(),
            'city'              => $this->faker->randomElement(['Montreal QC', 'Outremont QC', 'Cote-Saint-Luc QC']),
            'postal_code'       => $this->faker->postcode(),
            'email'             => $this->faker->safeEmail(),
            'home_phone'        => $this->faker->phoneNumber(),
            'mobile_phone'      => $this->faker->phoneNumber(),
            'wife_mobile_phone' => $this->faker->phoneNumber(),
            'shtibel'           => $this->faker->randomElement(['Zichron YY', 'Bais Yakov', 'Avreichim', 'CSL']),
            'father'            => $this->hebrewFaker()->name(),
            'father_in_law'     => $this->hebrewFaker()->name(),

            'member_since' => $membership ? $this->faker->date() : null,
            'active_membership' => $membership ? $this->faker->boolean(95) : null,
            'membership_type' => $membership ? $membershipType : null,
            'plan_type_id' => ($membership && $membershipType == Member::TYPE_MEMBERSHIP)
                ? PlanType::inRandomOrder()->first()->id
                : null
        ];
    }

    protected function hebrewFaker()
    {
        return app()->make(Generator::class, ['locale' => 'he_IL']);
    }
}
