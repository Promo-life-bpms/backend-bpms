<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CodeOrderDeliveryRoute extends Model
{
    use HasFactory;
    protected $fillable = [
        'delivery_route_id',
        'user_chofer_id',
        'type_of_chofer',
        'type_of_product',
        'code_sale',
        'code_order',
        'type_of_origin',
        'origin_address',
        'type_of_destiny',
        'destiny_address',
        'hour',
        'attention_to',
        'action',
        'num_guide',
        'observations',
        'status',
    ];

    public function productDeliveryRoute()
    {
        return $this->hasMany(ProductDeliveryRoute::class, 'code_order_route_id');
    }
    /*     public function productDeliveryRouteAllInformation()
    {
        return $this->hasMany(ProductDeliveryRoute::class, 'code_order_route_id')->with("completeInformation");
    } */

    public function deliveryRoute()
    {
        return $this->belongsTo(DeliveryRoute::class, 'delivery_route_id');
    }
}
