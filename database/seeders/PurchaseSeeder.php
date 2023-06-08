<?php

namespace Database\Seeders;

use App\Models\PurchaseRequest;
use Illuminate\Database\Seeder;

class PurchaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $i=0;
        for($i ==0; $i< 200; $i++){
            PurchaseRequest::create([
                'user_id' => rand(1,50),
                'company_id' => rand(1, 1),
                'spent_id' => rand(1, 25),
                'center_id' => rand(1, 19),  
                'description' => 'descripcion de prueba',
                'file' => '',
                'commentary' => 'Comentario de prueba',
                'purchase_status_id' => rand(1, 18),
                'payment_method_id' => rand(1, 3),
                'total' => rand(500, 300000),
                'status' => 0,
            ]);
    
        }
    }
}
