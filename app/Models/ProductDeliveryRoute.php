<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDeliveryRoute extends Model
{
    use HasFactory;
    protected $fillable = [
        'code_order_route_id',
        'odoo_product_id',
        'amount',
        'action',
        'hour',
        'observations',
        'provider',
        'origin_address',
        'destinity_address',
        'confirmation_sheet',
        'buyer_id',
        'files_reception_accepted' ,
        'tagger_user_id'

    ];

    public function completeInformation()
    {
        return $this->belongsTo(OrderPurchaseProduct::class, 'odoo_product_id', "odoo_product_id");
    }
    // code_order_route_id
    public function codeOrderRoute()
    {
        return $this->belongsTo(CodeOrderDeliveryRoute::class, 'code_order_route_id', "id");
    }
}
