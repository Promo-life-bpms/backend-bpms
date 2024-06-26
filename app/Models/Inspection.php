<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inspection extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        "code_inspection",
        'user_created_id',
        'date_inspection',
        'files',
        'type_product',
        'observations',
        'user_created',
        'user_signature_created',
        'user_reviewed',
        'user_signature_reviewed',
        'quantity_revised',
        'quantity_denied',
        'sample',
    ];

    public function productsSelected()
    {
        return $this->hasMany(InspectionProduct::class, 'inspection_id');
    }
    public function featuresQuantity()
    {
        return $this->hasMany(FeaturesQuantity::class);
    }
}
