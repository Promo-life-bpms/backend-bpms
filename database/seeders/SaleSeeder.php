<?php

namespace Database\Seeders;

use App\Models\AdditionalSaleInformation;
use App\Models\OrderPurchase;
use App\Models\OrderPurchaseProduct;
use App\Models\Sale;
use App\Models\SalesProduct;
use App\Models\SaleStatusChange;
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
        $sales =  Sale::factory()->count(20)->create();
        foreach ($sales as $sale) {
            SaleStatusChange::create([
                "sale_id" => $sale->id,
                "status_id" => 1,
            ]);
            AdditionalSaleInformation::factory()
                ->count(1)
                ->for($sale)
                ->create();
            SalesProduct::factory()
                ->count(rand(1, 5))
                ->for($sale)
                ->create();
            $orders = OrderPurchase::factory()
                ->count(rand(1, 3))
                ->for($sale)
                ->create();
            foreach ($orders as $order) {
                OrderPurchaseProduct::factory()
                    ->count(rand(1, 3))
                    ->for($order)
                    ->create();
            }
        }
    }
}
