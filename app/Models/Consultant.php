<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;



class Consultant extends Model
{
    protected $table = 'consultant';
    protected $fillable = [
        'id_user', 'name', 'cpf', 'birth_date'
    ];

}
