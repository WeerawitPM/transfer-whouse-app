<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Whouse extends Model
{
    protected $fillable = [
        'FCSKID',
        'FCCORP',
        'FCBRANCH',
        'FCCODE',
        'FCNAME'
    ];
}
