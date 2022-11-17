<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryRoute extends Model
{

    use HasFactory;

    // Datos de la tabla de ruta de entrega
    protected $fillable = [
        'date_of_delivery',
        'user_chofer_id',
        'type_of_product',
        'status'
        //
    ];
    public function productsDeliveryRoute()
    {
        return $this->hasMany(ProductDeliveryRoute::class, "delivery_route_id");

        //relacion hasmany() hacia product delivery route
    }
}
