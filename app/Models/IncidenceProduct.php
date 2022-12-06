<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class IncidenceProduct extends Model
{
    use HasFactory;
    public $table = "incidence_products";
    protected $fillable = [
        'id_order_purchase_products',
        'cantidad_seleccionada',
        'id_incidence'
    ];
}
