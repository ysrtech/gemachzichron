<?php

namespace Database\Factories;

use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentMethodFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PaymentMethod::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'type' => $this->faker->randomElement([
                PaymentMethod::TYPE_CREDIT_CARD,
                PaymentMethod::TYPE_PAD,
                PaymentMethod::TYPE_CHEQUE
            ]),
        ];
    }

    public function configure()
    {
        return $this->state(function (array $attributes) {

            switch ($attributes['type']) {
                case PaymentMethod::TYPE_CREDIT_CARD:
                    return [
                        'cc_number'          => $this->faker->creditCardNumber,
                        'cc_expiration'      => $this->faker->creditCardExpirationDateString,
                        'name_on_card'       => $this->faker->name,
                    ];
                case PaymentMethod::TYPE_PAD:
                    return [
                        'account_number'     => $this->faker->bankAccountNumber,
                        'transit_number'     => $this->faker->iban(),
                        'institution_number' => $this->faker->swiftBicNumber,
                    ];
                default:
                    return [];
            }
        });
    }
}
