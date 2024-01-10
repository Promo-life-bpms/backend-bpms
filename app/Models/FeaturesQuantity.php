<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeaturesQuantity extends Model
{
    use HasFactory;
    public $table = 'features_quantity';
    protected $fillable = [
        'inspection_id',
        'wrong_pantone_color',
        'damage_logo',
        'incorrect_logo',
        'incomplete_pieces',
        'merchandise_not_cut',
        'different_dimensions',
        'damaged_products',
        'product_does_not_perform_its_function',
        "wrong_product_code",
        'total',
    ];
}
