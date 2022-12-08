<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Remission extends Model
{
    use HasFactory;

    public $table = "remisiones";

    protected $fillable = [
        'code_remission',
        'comments',
        'satisfaction',
        'delivered',
        'delivery_signature',
        'received',
        'signature_received',
        'delivery_route_id',
        'user_chofer_id',
        'status'
        //
    ];
   
    public function productRemission()
    {
        return $this->hasMany(ProductRemission::class, "remission_id");

        //relacion hasmany() hacia product delivery route
    }
}
