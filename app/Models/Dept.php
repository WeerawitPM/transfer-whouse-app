<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dept extends Model
{
    protected $fillable = [
        'FCSKID',
        'FCCORP',
        'FCTYPE',
        'FCCODE',
        'FCNAME',
        'FCNAME2',
    ];
}
