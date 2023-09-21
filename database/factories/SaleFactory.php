<?php

namespace Database\Factories;

use App\Models\Status;
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
        $statuses = Status::all();
        return [
            'code_sale' => "PED" . $this->faker->unique()->numberBetween(0, 10600),
            'name_sale' => $this->faker->word(),
            'sequence' => "PEDIDO VENTA",
            'invoice_address' => $this->faker->address(),
            'delivery_address' => $this->faker->address(),
            'delivery_instructions' => $this->faker->address(),
            'delivery_time' => $this->faker->dateTimeBetween('+1 week', '+2 week'),
            'order_date' => $this->faker->dateTimeBetween('now', '+1 week'),
            'additional_information' => $this->faker->sentence(),
            'sample_required' => rand(0, 1),
            "labeling"=>$this->faker->word(),
            'tariff' => "MXN",
            'incidence' => true,
            'commercial_name' => $this->faker->name(),
            'commercial_email' => $this->faker->unique()->safeEmail(),
            'commercial_odoo_id' => $this->faker->numberBetween(0, 100),
            'subtotal' => $this->faker->numberBetween(0, 1000),
            'taxes' => $this->faker->numberBetween(0, 1000),
            'total' => $this->faker->numberBetween(0, 1000),
            'status_id' => $statuses[rand(0, count($statuses) - 1)]
        ];
    }
}
