<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefType extends Model
{
    protected $fillable = [
        'FCSKID',
        'FCCODE',
        'FCRETYPE',
        'FCNAME',
        'FCNAME2',
        'FCNGLNAME',
        'FCREPNAME'
    ];
}
