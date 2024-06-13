<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleStatusChange extends Model
{
    use HasFactory;

    protected $fillable = [
        "sale_id",
        "status_id",
        "status",
        "visible"
    ];

    public function pedido()
    {
        return $this->belongsTo(Sale::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }
}
