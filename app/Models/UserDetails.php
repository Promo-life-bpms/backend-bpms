<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetails extends Model
{
    use HasFactory;

    public $table = 'user_details';

    protected $fillable = [
        'id_user',
        'id_department',
        'id_company',
        'id_area',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function department(){
        return $this->belongsTo(Department::class);
    }

    public function company(){
        return $this->belongsTo(Company::class);
    }

    public function area(){
        return $this->belongsTo(Areas::class);
    }
}
