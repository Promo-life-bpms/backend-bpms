<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceptionProduct extends Model
{
    use HasFactory;
    protected $fillable = [
        'reception_id',
        'code_reception',
        'odoo_product_id',
        'product',
        'initial_demand',
        'done',
    ];

    public function completeInformation()
    {
        return $this->belongsTo(OrderPurchaseProduct::class, 'odoo_product_id', "odoo_product_id");
    }
}
