<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Sale extends Model
{
    // Pedido de Venta
    use HasFactory;

    protected $fillable = [
        'code_sale',
        'name_sale',
        'sequence',
        'invoice_address',
        'delivery_address',
        'delivery_time',
        'delivery_instructions',
        'order_date',
        "incidence",
        'sample_required',
        'labeling',
        'additional_information',
        'tariff',
        'commercial_odoo_id',
        'commercial_name',
        'commercial_email',
        'subtotal',
        'taxes',
        'total',
        'delivery_custom_address',
        'status_id',
    ];



    public function moreInformation()
    {
        return $this->hasOne(AdditionalSaleInformation::class);
    }

    public function saleProducts()
    {
        return $this->hasMany(SalesProduct::class);
    }

    public function user()
    {
        return $this->hasMany(User::class, 'email', 'commercial_email');
    }

    public function orders()
    {
        return $this->hasMany(OrderPurchase::class, "code_sale", "code_sale");
    }

    public function detailsOrders()
    {
        return $this->hasMany(OrderPurchase::class, "code_sale", "code_sale")->with('products');
    }

    public function ordersDeliveryRoute()
    {
        return $this->hasMany(CodeOrderDeliveryRoute::class, "code_sale", "code_sale")->with('productDeliveryRoute');
        //return $this->hasMany(CodeOrderDeliveryRoute::class, "code_sale", "code_sale")->with('productDeliveryRouteAllInformation');
    }

    public function currentStatus()
    {
        return $this->belongsTo(Status::class, 'status_id', 'id');
    }

    public function incidences()
    {
        return $this->hasMany(Incidence::class, 'code_sale', 'code_sale');
    }

    public function inspections()
    {
        return $this->hasMany(Inspection::class, 'sale_id');
    }

    public function routeDeliveries()
    {
        DB::statement("SET SQL_MODE=''");
        return $this->hasMany(CodeOrderDeliveryRoute::class, 'code_sale', 'code_sale')->groupBy('code_order_delivery_routes.delivery_route_id')->with('deliveryRoute', 'productDeliveryRoute');
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class, 'code_sale', 'code_sale');
    }

    public function lastStatus()
    {
        return $this->hasMany(SaleStatusChange::class);
       // return $this->hasOne(SaleStatusChange::class)->latestOfMany();
    }

    public function binnacles()
    {
        return $this->hasMany(Binnacle::class, 'sale_id');
    }

    // Ver Remissiones
    public function remissions()
    {
        return $this->hasMany(Remission::class, 'code_sale', 'code_sale');
    }
}

