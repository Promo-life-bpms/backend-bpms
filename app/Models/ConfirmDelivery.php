<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfirmDelivery extends Model
{
    use HasFactory;

    public $table = 'confirm_deliveries';

    protected $fillable = [
        'id_order_purchase_product',
        'delivery_type'
    ];

    public function OrderPurchaseProduct()
    {
        return $this->belongsTo(OrderPurchaseProduct::class, 'id_order_purchase_product');
    }
}
