<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPurchaseProduct extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_purchase_id',
        "odoo_product_id",
        "product",
        "description",
        "quantity_ordered",
        "quantity_delivered",
    ];

    public function orderPurchase()
    {
        return $this->belongsTo(OrderPurchase::class);
    }
}
