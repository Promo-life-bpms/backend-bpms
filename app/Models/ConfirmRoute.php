<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfirmRoute extends Model
{
    use HasFactory;

    public $table = 'confirm_routes';

    protected $fillable = [
        'id_product_order',
        'id_delivery_routes',
        'reception_type',
        'destination',
    ];

    public function OrderProduct()
    {
        return $this->belongsTo(OrderPurchaseProduct::class, 'id_product_order');
    }

    public function DeliveryRoutes()
    {
        return $this->belongsTo(DeliveryRoute::class, 'id_delivery_routes');
    }
}
