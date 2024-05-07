<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusDeliveryRouteChange extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_purchase_product_id',
        'code_order',
        'status',
        'visible'
    ];
}
