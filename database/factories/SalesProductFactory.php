<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SalesProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $quantity = rand(1, 2000);
        return [
            "odoo_product_id" => rand(1, 200),
            "product" => $this->faker->word() . ' ' . $this->faker->word(),
            "description" => $this->faker->sentence(),
            "provider" => $this->faker->company(),
            "logo" => $this->faker->company(),
            "quantity_ordered" => $quantity,
            "quantity_delivered" => rand(0, $quantity),
        ];
    }
}
