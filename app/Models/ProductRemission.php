<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductRemission extends Model
{
    use HasFactory;

    public $table = "product_remission";

    protected $fillable = [
        'remission_id',
        'delivered_quantity',
        "order_purchase_product_id",
        //
    ];

    public function remission()
    {
       // return $this->hasMany(CodeOrderDeliveryRoute::class, "id_remision");
    }
}
