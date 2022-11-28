<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDeliveryRoute extends Model
{
    use HasFactory;
    protected $fillable = [
        'code_order_route_id',
        'product',
        'amount',
    ];

    // public function deliveryRoute()
    //{
    //  return $this->belongsTo(CodeOrderDeliveryRoute::class, 'code_order_route_id');
    //}
}
