<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultantAddress extends Model
{
    protected $table = 'consultant_address';
    protected $fillable = [
        'id_consultant', 'id_address', 'number', 'complement', 'acting_city'
    ];
}
