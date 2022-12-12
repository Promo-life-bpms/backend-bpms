<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reception extends Model
{
    use HasFactory;

    protected $fillable = [
        'code_reception',
        'code_order',
        'company',
        'type_operation',
        'planned_date',
        'effective_date',
        'status',
    ];

    public function productsReception()
    {
        return $this->hasMany(ReceptionProduct::class);
    }
}
