<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaggerProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'odoo_id',
        'name_user',
        'email',
        'name_provider',
    ];
}
