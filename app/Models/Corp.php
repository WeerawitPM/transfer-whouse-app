<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Corp extends Model
{
    protected $fillable = [
        'FCSKID',
        'FCCODE',
        'FCNAME',
        'FCTAXID',
        'FCADDR1',
        'FCADDR2',
        'FCTEL',
        'FCFAX'
    ];
}
