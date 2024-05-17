<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryRoute extends Model
{

    use HasFactory;

    // Datos de la tabla de ruta de entrega
    protected $fillable = [
        'code_sale',
        'code_order',
        'product_id',
        'type',
        'type_of_destiny',
        'date_of_delivery',
        'status_delivery',
        'shipping_type',
        'color',
        'visible',
    ];

    /*  public function codeOrderDeliveryRoute()
    {
        return $this->hasMany(CodeOrderDeliveryRoute::class, "delivery_route_id");
    } */

    public function remissions()
    {
        return $this->hasMany(Remission::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_chofer_id', 'id');
    }
}
