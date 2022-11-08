<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'code_sale' => "PED" . $this->faker->numberBetween(0, 600),
            'name_sale' => $this->faker->word(),
            'invoice_address' => $this->faker->address(),
            'delivery_address' => $this->faker->address(),
            'delivery_instructions' => $this->faker->address(),
            'delivery_time' => $this->faker->dateTimeBetween('+1 week', '+2 week'),
            'confirmation_date' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'order_date' => $this->faker->dateTimeBetween('now', '+1 week'),
            'additional_information' => $this->faker->sentence(),
            'commercial_name' => $this->faker->name(),
            'commercial_email' => $this->faker->email(),
            'commercial_odoo_id' => $this->faker->numberBetween(0, 100)
        ];
    }
}
