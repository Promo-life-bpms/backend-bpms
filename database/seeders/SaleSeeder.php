<?php

namespace Database\Seeders;

use App\Models\AdditionalSaleInformation;
use App\Models\Sale;
use Illuminate\Database\Seeder;

class SaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sales =  Sale::factory()->count(100)->create();
        foreach ($sales as $sale) {
            AdditionalSaleInformation::factory()
                ->count(1)
                ->for($sale)
                ->create();
        }
    }
}
