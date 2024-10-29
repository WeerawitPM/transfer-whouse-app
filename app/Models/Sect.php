<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sect extends Model
{
    protected $fillable = [
        'FCSKID',
        'FCCORP',
        'FCDEPT',
        'FCCODE',
        'FCNAME',
        'FCNAME2',
    ];
}
