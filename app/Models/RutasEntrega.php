<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RutasEntrega extends Model
{
    use HasFactory;
    public $table = "rutasdeentrega";
    protected $fillable = ['pedido','num_orden'];

}
