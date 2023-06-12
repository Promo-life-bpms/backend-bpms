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

            $status = rand(1, 4);
            $type = 'producto';
            $purchase_status = 'normal';
            $approved = 'pendiente';
            $approved_by = null;
            if($status == 1){
                $type = 'producto';
            }else{
        
                $randon_type = rand(1, 3);
               
                if($status ==2){
                    if($randon_type ==2){
                        $purchase_status = 'cancelado';
                    }
                }

                if($status ==4){
                    if($randon_type ==3){
                        $purchase_status = 'devolucion';
                    }
                }

                $approved = 'aprobada';
                $approved_by = rand(1, 50);
            }
            if(rand(1, 5) == 2 ){
                $type = 'servicio';
            }

            PurchaseRequest::create([
                'user_id' => rand(1,50),
                'company_id' => rand(1, 2),
                'spent_id' => rand(1, 25),
                'center_id' => rand(1, 19),  
                'description' => 'descripcion de prueba',
                'file' => '',
                'commentary' => 'Comentario de prueba',
                'purchase_status_id' => rand(1, 4),
                'type' =>  $type,
                'type_status' => $purchase_status,
                'payment_method_id' => rand(1, 3),
                'total' => rand(500, 300000),
                'approved_status' => $approved,
                'approved_by' => $approved_by,
            ]);
    
        }
    }
}
