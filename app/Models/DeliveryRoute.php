<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryRoute extends Model
{

    use HasFactory;

    // Datos de la tabla de ruta de entrega
    protected $fillable = [
        'code_route',
        'is_active',
        'date_of_delivery',
        'user_chofer_id',
        'type_of_chofer',
        'type_of_product',
        'status',
    ];

    public function codeOrderDeliveryRoute()
    {
        //relacion hasmany() hacia product delivery route
        return $this->hasMany(CodeOrderDeliveryRoute::class, "delivery_route_id");
    }

    public function remissions()
    {
        return $this->hasMany(Remission::class);
    }
}
