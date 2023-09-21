<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusProductsOT extends Model
{
    protected $fillable = [
        'id_status_o_t_s',
        'id_order_purchase_products',
        'cantidad_seleccionada',
    ];

    public function completeInformation()
    {
        return $this->belongsTo(OrderPurchaseProduct::class, 'id_order_purchase_products', 'id');
    }
}
