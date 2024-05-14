<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfirmProductCount extends Model
{
    use HasFactory;

    public $table = 'confirm_product_counts';

    protected $fillable = [
        'id_product',
        'type',
        'confirmation_type',
        'observation',
        'id_confirm_routes',
    ];

    public function ConfirmRoute(){
        return $this->belongsTo(ConfirmRoute::class, 'id_confirm_routes');
    }
}
