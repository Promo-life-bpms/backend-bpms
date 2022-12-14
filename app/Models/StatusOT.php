<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusOT extends Model
{
    use HasFactory;

    protected $fillable = [
        'hora',
        'id_order_purchase',
        'status',
        'id_order_purchase_products',
        'cantidad_seleccionada',


        //COLACAR TODOS LOS CAMPOS CORRESPONDIENTES DE TODA LA TABLA
    ];

    public function StatusOT()
    {
        return $this->hasMany(StatusOT::class);
    }
}
