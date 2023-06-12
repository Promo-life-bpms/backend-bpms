<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequest extends Model
{
    use HasFactory;

    public $table = "purchase_requests";

    protected $fillable = [
        'user_id',
        'company_id',
        'spent_id',
        'center_id',
        'description',
        'file',
        'commentary',
        'purchase_status_id',
        'type',
        'type_status',
        'payment_method_id',
        'total',
        'sign',
        'approved_status',
        'approved_by',
    ];


    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function spent()
    {
        return $this->belongsTo(Spent::class);
    }

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function purchase_status()
    {
        return $this->belongsTo(PurchaseStatus::class);
    }
    
    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function purchase_devolution()
    {
        return $this->hasOne(PurchaseDevolution::class);
    }


}
