<?php

namespace Database\Factories;

use App\Models\Loan;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Loan::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'amount'           => $this->faker->randomNumber(3) . 00,
            'loan_date'        => $this->faker->dateTimeBetween(),
            'cheque_number'    => $this->faker->randomNumber(5),
            'application_copy' => null,
        ];
    }
}
