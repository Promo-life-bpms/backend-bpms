<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incidence extends Model
{
    use HasFactory;

    protected $fillable = [
        'num_incidencia',
        'area',
        'motivo',
        'tipo_de_producto',
        'tipo_de_tecnica',
        'solucion_de_incidencia',
        'responsable',
        'fecha_creacion',
        'status',
        'evidencia',
        'fecha_compromiso',
        'solucion',
        'fecha_solucion',
        'id_user',
        'elaboro',
        'firma_elaboro',
        'reviso',
        'firma_reviso',
        'comentarios_generales',
        'id_sales'
    ];

    public function incidenciaProducto()
    {
        return $this->hasMany(IncidenceProduct::class, "id_incidence");
    }
}
