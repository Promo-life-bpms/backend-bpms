<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class OrderPurchaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $company = ['PROMO LIFE', "BH TRADEMARKET"];
        $code = ["OC", "OT"];
        $seq = ["COMPRAS PEDIDO", "COMPRAS MAQUILA"];
        $status =["Confirmado","En Proceso","Entregado","Cancelado"];
        return [
            'code_order' => $code[rand(0, 1)] . $this->faker->unique()->numberBetween(0, 10600),
            'provider_name' => $this->faker->company(),
            'sequence' => $seq[rand(0, 1)],
            'order_date' => $this->faker->dateTimeBetween('now', '+1 week'),
            'planned_date' => $this->faker->dateTimeBetween('+1 week', '+2 week'),
            'deliver_in' => $this->faker->address(),
            'company' => $company[rand(0, 1)],
            'status' => $status[rand(0,2)]
        ];
    }
}
