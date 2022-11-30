<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalSaleInformation extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'client_name',
        'client_address',
        'client_contact',
        'warehouse_company',
        'warehouse_address',
        'delivery_policy',
        'schedule_change',
        'reason_for_change',
        'planned_date',
        'commitment_date',
        'effective_date'
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
