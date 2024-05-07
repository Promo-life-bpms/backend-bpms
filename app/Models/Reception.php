<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reception extends Model
{
    use HasFactory;

    protected $fillable = [
        'code_order',
        'product_id',
        'type',
        'date_of_reception',
        'destiny',
    ];

    public function productsReception()
    {
        return $this->hasMany(ReceptionProduct::class, "reception_id", "id");
    }
}
