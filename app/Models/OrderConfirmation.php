<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderConfirmation extends Model
{
    use HasFactory;

    public $table = 'order_confirmations';

    protected $fillable = [
        'status',
        'description',
        'code_sale',
        'order_purchase_id',
        'id_order_products',
    ];

    public function OrderPurchase(){
        return $this->belongsTo(OrderPurchase::class,'order_purchase_id');
    }

    public function OrderProducts(){
        return $this->belongsTo(OrderPurchaseProduct::class, 'id_order_products');
    }
}
