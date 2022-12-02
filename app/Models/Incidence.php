<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incidence extends Model
{
    use HasFactory;

    protected $fillable = [
        'motivo_area',
        'motivo',
        'tipo_de_producto',
        'tipo_de_tecnica',
        'solucion_de_incidencia',
        'responsable',
        'status',
        'evidencia',
        'fecha_compromiso',
        'elaboro',
        'firma_elaboro',
        'reviso',
        'firma_reviso',
        'comentarios_generales',
        'id_sales'
    ];
}
