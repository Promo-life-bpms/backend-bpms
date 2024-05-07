<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfirmRouteReceipt extends Model
{
    use HasFactory;

    public $table = ['confirm_route_receipts'];

    protected $fillable = [
        'id_order_purchase_products',
        'reception_type',
        'destination'
    ];

    public function OrderProducs()
    {
        return $this->belongsTo(OrderPurchaseProduct::class, 'id_order_purchase_products');
    }
}
