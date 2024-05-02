<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class ModificacionesBPMS extends Seeder
{
    
    public function run()
    {
        $statusSale= array(
            (object)[
                'status' => 'Pedido confirmado',
                'slug' =>  'pedido-confirmado',  
            ],
            (object)[
                'status' => 'OC/OT creadas',
                'slug' =>  'oc-ot-creadas',  
            ],
            (object)[
                'status' => 'Recibido en almacén',
                'slug' =>  'recibido-en-almacen',  
            ],
            (object)[
                'status' => 'Enviado a maquila',
                'slug' =>  'enviado a maquila',  
            ],
            (object)[
                'status' => 'Conteo del producto',
                'slug' =>  'conteo-del-producto',  
            ],
            (object)[
                'status' => 'Inspección de producto',
                'slug' =>  'inspeccion-de-producto',  
            ],
            (object)[
                'status' => 'Proceso de entrega al cliente',
                'slug' =>  'proceso-de-entrega-al-cliente',  
            ],
            (object)[
                'status' => 'Entregado al cliente',
                'slug' =>  'entregado-al-cliente',  
            ],
        );

        foreach ($statusSale as $statuSale){
            Status::create([
                'status' => $statuSale->status,
                'slug' => $statuSale->slug,
            ]);
        }
    }
}
