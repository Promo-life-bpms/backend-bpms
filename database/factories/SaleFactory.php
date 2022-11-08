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
            'code_sale' => "PED" . $this->faker->unique()->numberBetween(0, 10600),
            'name_sale' => $this->faker->word(),
            'sequence' => "PEDIDO VENTA",
            'invoice_address' => $this->faker->address(),
            'delivery_address' => $this->faker->address(),
            'delivery_instructions' => $this->faker->address(),
            'delivery_time' => $this->faker->dateTimeBetween('+1 week', '+2 week'),
            'confirmation_date' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'order_date' => $this->faker->dateTimeBetween('now', '+1 week'),
            'additional_information' => $this->faker->sentence(),
            'sample_required' => rand(0, 1),
            'tariff' => "MXN",
            'commercial_name' => $this->faker->name(),
            'commercial_email' => $this->faker->unique()->safeEmail(),
            'commercial_odoo_id' => $this->faker->numberBetween(0, 100)
        ];
    }
}
