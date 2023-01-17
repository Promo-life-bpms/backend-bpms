<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class OrderPurchaseProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $company = ['PROMO LIFE', "BH TRADEMARKET"];
        $quantity = rand(1, 2000);
        return [
            "odoo_product_id" => rand(1, 200),
            "product" => $this->faker->word() . ' ' . $this->faker->word(),
            "description" => $this->faker->sentence(),
            'planned_date' => $this->faker->dateTimeBetween('+1 week', '+2 week'),
            'company' => $company[rand(0, 1)],
            "quantity" => $quantity,
            "quantity_invoiced" => $quantity,
            "quantity_delivered" => rand(0, $quantity),
            'measurement_unit' => "Pieza",
            'unit_price' => $this->faker->numberBetween(0, 100),
            'subtotal' => $this->faker->numberBetween(0, 1000),
        ];
    }
}
