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
        'description',
        'file',
        'commentary',
        'purchase_status_id',
        'payment_method_id',
        'total'
    ];


    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function company()
    {
        return $this->hasOne(Company::class);
    }

    public function spent()
    {
        return $this->hasOne(Spent::class);
    }

    public function purchase_status()
    {
        return $this->hasOne(PurchaseStatus::class);
    }
    
    public function payment_method()
    {
        return $this->hasOne(PaymentMethod::class);
    }
}
