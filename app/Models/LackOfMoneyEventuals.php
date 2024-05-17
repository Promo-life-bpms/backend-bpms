<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LackOfMoneyEventuals extends Model
{
    use HasFactory;

    public $table = 'lack_of_money_eventuals';

    protected $fillable = [
        'id_applicant_person',
        'id_person_who_delivers',
        'description',
        'file',
        'previous_total',
        'current_total',
        'status',
        'confirmation_datetime',
        'id_eventual',
        'id_purchase'
    ];

    public function Eventual(){
        return $this->hasOne(Eventuales::class, 'id_eventual');
    }
    public function RequestPurchase(){
        return $this->hasOne(PurchaseRequest::class, 'id_purchase');
    }
    //hasOne
}
