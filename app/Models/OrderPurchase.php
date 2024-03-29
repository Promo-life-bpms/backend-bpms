<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPurchase extends Model
{
    use HasFactory;
    protected $fillable = [
        'code_order',
        'code_sale',
        'provider_name',
        'provider_address',
        'supplier_representative',
        'sequence',
        'order_date',
        'planned_date',
        'company',
        'status',
        'status_bpm',
        'type_purchase',
        'total',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class, "code_sale", "code_sale");
    }

    public function products()
    {
        return $this->hasMany(OrderPurchaseProduct::class);
    }

    public function receptions()
    {
        return $this->hasMany(Reception::class, "code_order", "code_order");
    }
    public function receptionsConfirmated()
    {
        return $this->hasMany(ReceptionConfirmationMaquilado::class, "code_order", "code_order");
    }

    public function theirHistoryStatus()
    {
        return $this->hasMany(StatusOT::class, "id_order_purchases", "id")->with("StatusProductsOT");
    }
    public function lastStatusOT()
    {
        return $this->hasOne(StatusOT::class, "id_order_purchases", "id")->latestOfMany();
    }

    public function receptionsWithTheirProducts()
    {
        return $this->hasMany(Reception::class, "code_order", "code_order")->with("productsReception");
    }

    public function codeOrderDeliveryRoute($route_id)
    {
        //relacion hasmany() hacia product delivery route
        return $this->hasOne(CodeOrderDeliveryRoute::class, "code_order", "code_order")->where("code_order_delivery_routes.delivery_route_id", $route_id)->first();
    }
}
