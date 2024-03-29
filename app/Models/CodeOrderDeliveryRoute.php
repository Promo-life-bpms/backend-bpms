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
        'parcel_id',
        'parcel_name',
        'type_of_chofer',
        'type_of_product',
        'code_sale',
        'code_order',
        'type_of_origin',
        'type_of_destiny',
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

    // code_order
    public function orderPurchase()
    {
        return $this->belongsTo(OrderPurchase::class, 'code_order', "code_order");
    }
}
