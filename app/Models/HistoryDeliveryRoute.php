<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryDeliveryRoute extends Model
{
    use HasFactory;
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
}
