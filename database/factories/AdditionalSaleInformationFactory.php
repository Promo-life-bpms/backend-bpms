<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AdditionalSaleInformationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $companyWareHouse = ['PROMO LIFE', "BH TRADEMARKET"];
        return [
            'client_name' => $this->faker->company(),
            'client_contact' => $this->faker->name(),
            'warehouse_company' => $companyWareHouse[round(0, 1)],
            'warehouse_address' => $this->faker->address(),
            'delivery_policy' => $this->faker->text(),
            'reason_for_change' => $this->faker->text(),
            'schedule_change' => round(0, 1),
            'planned_date' => $this->faker->dateTimeBetween('+1 week', '+2 week'),
            'commitment_date' => $this->faker->dateTimeBetween('+1 week', '+2 week'),
            'effective_date' => $this->faker->dateTimeBetween('+1 week', '+2 week')
        ];
    }
}
