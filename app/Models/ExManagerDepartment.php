<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExManagerDepartment extends Model
{
    use HasFactory;

    public $table = 'ex_manager_departments';

    protected $fillable = [
        'id_manager_has_department',
        'user_who_deleted',
        'ex_manager',
        'id_department'
    ];

    public function user(){

        return $this->belongsTo(User::class, 'ex_manager');
    }

    public function department(){
        return $this->belongsTo(Department::class, 'id_department');

    }
    
}
