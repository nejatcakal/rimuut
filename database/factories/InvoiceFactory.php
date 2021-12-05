<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'description' => $this->faker->sentence,
            'date' => $this->faker->date,
            'send_to' => factory(App\User::class),
            'currency' => 'TRY',
            'total' => 100,
            'tax_rate' => 5,
            'tax_amount' => 5,
            'grand_total' => 105,
        ];
    }
}
