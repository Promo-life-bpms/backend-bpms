<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusOT extends Model
{
    use HasFactory;

    protected $fillable = [
        'hora',
        'id_order_purchases',
        'status',


        //COLACAR TODOS LOS CAMPOS CORRESPONDIENTES DE TODA LA TABLA
    ];
    public function StatusProductsOT()
    {
        return $this->hasMany(StatusProductsOT::class, "id_status_o_t_s");
    }
}
