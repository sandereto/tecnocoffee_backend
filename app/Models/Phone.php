<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{
    protected $table = 'phone';
    protected $fillable = [
        'number', 'id_type_phone', 'id_consultant'
    ];
}
