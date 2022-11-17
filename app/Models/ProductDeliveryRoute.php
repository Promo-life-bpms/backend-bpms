<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDeliveryRoute extends Model
{
    use HasFactory;
    protected $fillable = [
        'delivery_route_id',
        'id_order',
        'num_order',
        'type_of_origin',
        'delivery_address',
        'type_of_destiny',
        'destiny_address',
        'hour',
        'attention_to',
        'action',
        'num_guide',
        'observations',


    ];

    public function deliveryRoute()
    {
        return $this->belongsTo(DeliveryRoute::class, 'delivery_route_id');
    }
}
